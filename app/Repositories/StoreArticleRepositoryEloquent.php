<?php

namespace App\Repositories;

use App\Entities\ArticleImages;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\StoreArticleRepository;
use App\Entities\StoreArticle;
use App\Validators\StoreArticleValidator;

/**
 * Class StoreArticleRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class StoreArticleRepositoryEloquent extends BaseRepository implements StoreArticleRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return StoreArticle::class;
    }

    /**
    * Specify Validator class name
    *
    * @return mixed
    */
    public function validator()
    {

        return StoreArticleValidator::class;
    }


    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    /**
     * store images articles of store
     */
    public function storeImages($imagePath, $articleId)
    {
        $data = [
            'article_id' => $articleId,
            'img_path' => $imagePath,
        ];
        ArticleImages::create($data);

        return true;
    }

    /**
     * get articles of store
     */
    public function getArticleByStoreId($storeId)
    {
        return $this->model->where('store_id', $storeId)
                    ->with('images')
                    ->inRandomOrder();
    }

    /**
     * get articles by id
     */
    public function getArticlesById($id)
    {
        return $this->model->where('id', $id)
                        ->with('images')
                        ->first();
    }

    /**
     * get images of articles by id
     */
    public function getImageArticles($id)
    {
        return ArticleImages::where('id', $id)->first();
    }

    /**
     * remove images of articles by image_id
     */
    public function removeImageById($id)
    {
        return ArticleImages::where('id', $id)->delete();
    }

    /**
     * get all images of article
     */
    public function getAllImagesArticles($storeId)
    {
        return $this->model->rightJoin('article_images', 'store_articles.id', 'article_images.article_id')
                        ->where('store_articles.store_id', $storeId)
                        ->select(
                            'article_images.id',
                            'article_images.img_path',
                            'store_articles.store_id',
                        );
    }
}
