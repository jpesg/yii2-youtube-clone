<?php
//3.53.57
namespace frontend\controllers;

use common\models\Video;
use common\models\VideoLike;
use common\models\VideoView;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class VideoController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['like', 'dislike', 'history'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@']
                    ]
                ]

            ],
            'verb' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'like' => ['post'],
                    'dislike' => ['post']
                ]
            ]
        ];
    }

    public function actionIndex()
    {
        $this->layout = 'main';

        $dataProvider = new ActiveDataProvider([
            'query' => Video::find()->with('createdBy')->published()->latest(),
            'pagination' => [
                'pageSize' => 5
            ]
        ]);
        return $this->render('index', [
            'dataProvider' => $dataProvider
        ]);
    }

    public function actionView($id)
    {
        $this->layout = 'auth';

        $video = self::findVideo($id);

        $videoView = new VideoView();
        $videoView->video_id = $id;
        $videoView->user_id = \Yii::$app->user->id;
        $videoView->created_at = time();
        $videoView->save();

        $similarVideos = Video::find()
            ->published()
            ->andWhere((['NOT', ['video_id' => $id]]))
            ->byKeyword($video->title)
            ->limit(10)->all();

        return $this->render('view', [
            'model' => $video,
            'similarVideos' => $similarVideos
        ]);
    }

    public function actionLike($id)
    {
        $video = self::findVideo($id);

        $userId = \Yii::$app->user->id;

        $videoLikeDislike = VideoLike::find()
            ->userIdVideoId($userId, $id)
            ->one();

        if (!$videoLikeDislike) {
            self::saveLikeDislike($id, $userId, VideoLike::TYPE_LIKE);
        } else if ($videoLikeDislike->type === VideoLike::TYPE_LIKE) {
            $videoLikeDislike->delete();
        } else {
            //delete dislike
            $videoLikeDislike->delete();
            self::saveLikeDislike($id, $userId, VideoLike::TYPE_LIKE);
        }

        return $this->renderAjax('_buttons', [
            'model' => $video
        ]);
    }

    public function actionDislike($id)
    {
        $video = self::findVideo($id);

        $userId = \Yii::$app->user->id;

        $videoLikeDislike = VideoLike::find()
            ->userIdVideoId($userId, $id)
            ->one();

        if (!$videoLikeDislike) {
            self::saveLikeDislike($id, $userId, VideoLike::TYPE_DISLIKE);
        } else if ($videoLikeDislike->type === VideoLike::TYPE_DISLIKE) {
            $videoLikeDislike->delete();
        } else {
            //delete like
            $videoLikeDislike->delete();
            self::saveLikeDislike($id, $userId, VideoLike::TYPE_DISLIKE);
        }

        return $this->renderAjax('_buttons', [
            'model' => $video
        ]);
    }

    public function actionSearch($keyword)
    {
        $this->layout = 'main';
        $query = Video::find()
            ->with('createdBy')
            ->published()
            ->latest();

        if ($keyword) {
            $query->byKeyword($keyword)
                //todo pass keyword as parameter
                ->orderBy("MATCH(title,description,tags) AGAINST($keyword)");
        }
        $dataProvider = new ActiveDataProvider([
            'query' => $query
        ]);
        return $this->render('search', [
            'dataProvider' => $dataProvider
        ]);
    }

    public function actionHistory()
    {
        $this->layout = 'main';
        $query = Video::find()
            ->alias('v')
            ->innerJoin('(select video_id, MAX(created_at) as max_date FROM video 
            WHERE user_id = :userId  
            GROUP BY video_id) vv', 'vv.video_id = v.video_id', ['userId' => \Yii::$app->user->id])
            ->orderBy('vv.max_date DESC');


        $dataProvider = new ActiveDataProvider([
            'query' => $query
        ]);
        return $this->render('history', [
            'dataProvider' => $dataProvider
        ]);
    }

    protected function findVideo($id)
    {
        $video = Video::findOne($id);
        if (!$video) {
            throw new NotFoundHttpException('Video Not Found');
        }
        return $video;
    }

    protected function saveLikeDislike($videoId, $userId, $type)
    {
        $videoLike = new VideoLike();
        $videoLike->video_id = $videoId;
        $videoLike->user_id = $userId;
        $videoLike->created_at = time();
        $videoLike->type = $type;
        $videoLike->save();
    }


}