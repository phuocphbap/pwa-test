<?php

namespace App\Services;

use App\Repositories\StoreRepository;
use App\Helpers\Facades\UploadS3Helper;
use Illuminate\Http\Exceptions\HttpResponseException;
class StoreService
{
    protected $storeRepo;

    public function __construct(StoreRepository $storeRepo)
    {
        $this->storeRepo = $storeRepo;
    }

    /**
     * handle get map of store.
     */
    public function handleGetImagesMap($data)
    {
        if (!$data->store_image_map) {
            $latitude = $data->latitude;
            $longitude = $data->longitude;
            if ($latitude && $longitude) {
                $src = 'http://maps.google.com/maps/api/staticmap?center='.$latitude.','.$longitude.'&markers=color:red%7Clabel:C%7C'.$latitude.','.$longitude.'&zoom=16&size=600x400&key='.env('GOOGLE_CLOUD_KEY');
                $headers = get_headers($src, 1);
                if (strpos($headers['Content-Type'], 'image/') !== false) {
                    $imageLink = UploadS3Helper::uploadImage($src, 'store');
                    if ($imageLink) {
                        $this->storeRepo->update(['image_map' => $imageLink], $data->id);
                    }
                }
            }
        }

        return $this->storeRepo->getAllDetailStoreById($data->id);
    }

    /**
     * get spot store.
     */
    public function getSpotStore($latitude, $longitude, $user, $search, $regionId)
    {
        return $this->storeRepo->getSpotStore($latitude, $longitude, $user, $search, $regionId);
    }


    /**
     * get place search by google cloud.
     */
    public function getPlaceByPlaceID($place_id)
    {
        $API_KEY = env('GOOGLE_CLOUD_KEY');
        $request = 'https://maps.googleapis.com/maps/api/place/details/json?';
        $params = [
            'place_id' => $place_id,
            'key' => $API_KEY,
            'language' => 'ja',
        ];
        $request .= http_build_query($params);
        $json = file_get_contents($request);
        $data = json_decode($json, true);

        return $data['result']['geometry']['location'];
    }

    /**
     * get place search by google cloud.
     */
    public function getPlaceSearch($text)
    {
        $API_KEY = env('GOOGLE_CLOUD_KEY');
        $request = 'https://maps.googleapis.com/maps/api/place/autocomplete/json?';
        $params = [
            'input' => $text,
            'key' => $API_KEY,
            'language' => 'ja',
        ];
        $request .= http_build_query($params);
        $json = file_get_contents($request);
        $data = json_decode($json, true);

        $response = [];
        foreach ($data['predictions'] as $key => $value) {
            $response[$key]['address'] = $value['description'];
            $response[$key]['place_id'] = $value['place_id'];
        }

        return $response;
    }

    /**
     * get place search by google cloud.
     */
    public function getPlaceTextSearch($text)
    {
        $API_KEY = env('GOOGLE_CLOUD_KEY');

        $request = 'https://maps.googleapis.com/maps/api/place/textsearch/json?';
        $params = [
            'query' => $text,
            'key' => $API_KEY,
            'language' => 'ja',
        ];
        $request .= http_build_query($params);
        $json = file_get_contents($request);
        $data = json_decode($json, true);

        $response = [];
        foreach ($data['results'] as $key => $value) {
            $response[$key]['address'] = $value['formatted_address'];
            $response[$key]['location'] = $value['geometry']['location'];
        }

        return $response;
    }
    
    /**
     * handleUpdateInfoStore
     *
     * @param mixed $user
     * @param mixed $request
     *
     * @return void
     */
    public function handleUpdateInfoStore($user, $request)
    {
        
        $storeId = $request->storeId;
        $phone = $request->phone ?? null;
        $address = $request->address ?? null;
        $address_id = $request->address_id ?? null;
        $place_id = $request->place_id ?? null;
        //check place_id and get localtion
        if(isset($place_id) && !empty($place_id)) {
            $place_detail = $this->getPlaceByPlaceID($request->place_id);
            $latitude = $place_detail['lat'] ?? null;
            $longitude = $place_detail['lng'] ?? null;
        }

        $store = $this->storeRepo->find($storeId);
        if ($store && $store->user_id != $user->id) {
            $response = response()->json(['errors' => true, 'messages' => __('api.common.you_not_permission')]);
            throw new HttpResponseException($response);
        }
        
        if ($store && $store->image_map && isset($address) && isset($latitude) && isset($longitude)) {
            UploadS3Helper::deleteImage($store->image_map);
            $this->storeRepo->update([
                'image_map' => null,
            ], $store->id);
        }
        // store save without address
        if(!isset($address) || empty($address)) {
            return $this->storeRepo->where('id', $storeId)
                        ->update([
                            'store_address' => null,
                            'address_id' => $address_id,
                            'image_map' => null,
                            'latitude' => null,
                            'longitude' => null,
                            'phone' => $phone,
                            'place_id' => null,
                        ]);
        }
        // store save new address without place_id
        if((isset($address) && !isset($place_id)) || empty($place_id)) {
            return $this->storeRepo->where('id', $storeId)
                        ->update([
                            'store_address' => $address,
                            'address_id' => $address_id,
                            'image_map' => null,
                            'latitude' => null,
                            'longitude' => null,
                            'phone' => $phone,
                            'place_id' => null,
                        ]);
        }
        // store new image map with long & lat
        if ((isset($address) && isset($place_id)) || !empty($place_id)) {
            $src = 'http://maps.google.com/maps/api/staticmap?center='.$latitude.','.$longitude.'&markers=color:red%7Clabel:C%7C'.$latitude.','.$longitude.'&zoom=16&size=600x400&key='.env('GOOGLE_CLOUD_KEY');
            $headers = get_headers($src, 1);
            if (strpos($headers['Content-Type'], 'image/') !== false) {
                $imageLink = UploadS3Helper::uploadImage($src, 'store');
                if ($imageLink) {
                    return $this->storeRepo->where('id', $storeId)
                            ->update([
                                'store_address' => $address,
                                'address_id' => $address_id,
                                'image_map' => $imageLink,
                                'latitude' => $latitude,
                                'longitude' => $longitude,
                                'phone' => $phone,
                                'place_id' => $place_id,
                            ]);
                } else {
                    $response = response()->json(['errors' => true, 'messages' => __('api.store.upload_file_s3_error')]);
                    throw new HttpResponseException($response);
                }
            }
            $response = response()->json(['errors' => true, 'messages' => __('api.store.get_image_map_google_cloud_has_error')]);
            throw new HttpResponseException($response);
        } 
    }
}
