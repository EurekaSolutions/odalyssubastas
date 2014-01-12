<?php

class SiteController extends Controller
{
	/**
	 * Declares class-based actions.
	 */
	public function actions()
	{
		return array(
			// captcha action renders the CAPTCHA image displayed on the contact page
			'captcha'=>array(
				'class'=>'CCaptchaAction',
				'backColor'=>0xFFFFFF,
			),
			// page action renders "static" pages stored under 'protected/views/site/pages'
			// They can be accessed via: index.php?r=site/page&view=FileName
			'page'=>array(
				'class'=>'CViewAction',
			),
		);
	}

	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionIndex()
	{
		// renders the view file 'protected/views/site/index.php'
		// using the default layout 'protected/views/layouts/main.php'
		$this->render('index');
	}


	public function actionBuscar()
	{
		// renders the view file 'protected/views/site/index.php'
		// using the default layout 'protected/views/layouts/main.php'
		//$this->render('index');

		//$connection Yii::app()->db;
		//$connection->query("SELECT * FROM imagen_s WHERE ");

		$criteria = new CDbCriteria;

		$criteria->condition = 'activa=:activa';
		$criteria->params = array(':activa'=>1);

		$subas = Subastas::model()->find($criteria);


		$criteria = new CDbCriteria;

		$criteria->condition = 'ids=:ids';
		$criteria->params = array(':ids'=>$subas['id']);


		$query = ImagenS::model()->findAll($criteria);

		foreach ($query as $key => $value) {
			$criteria = new CDbCriteria;

			$criteria->condition = 'idusuario=:idusuario';
			$criteria->params = array(':idusuario'=>$value->id_usuario);

			$resultado= Usuariospujas::model()->find($criteria);

			if($resultado)
			{

				echo 'Paleta Usuario: '.$resultado['paleta'].' | Imagens ID: '.$value->id.' | Precio Base: '.$value->base.' | Precio Actual: '.$value->actual.'<br/>';

			}else
			{
				echo 'Imagens ID: '.$value->id.' | Precio Base: '.$value->base.' | Precio Actual: '.$value->actual.'<br/>';

			}
		}



		/*$criteria = new CDbCriteria;
		//$criteria->alias = 'a';
		$criteria->select = '*';
		$criteria->join = 'LEFT JOIN usuariospujas ON usuariospujas.idusuario = imagen_s.id_usuario';
		$criteria->condition='ids=:ids';
		//$criteria->addCondition("imagen_s.ids=".34);
		$criteria->together = true;
		$criteria->params=array(':ids'=>$subas['id']);
		
		//When using findAll() the results will be returned in array form, 
		//while using find() the result will be in object form.
		$query = ImagenS::model()->findAll($criteria);

		$criteria = new CDbCriteria;
		//$criteria->join = 'LEFT JOIN usuarios ON usuarios.id=idsubasta';
		$criteria->condition='idusuario=:idusuario';
		if(isset($subas['id_usuario']))
		$criteria->params=array(':idusuario'=>$subas['id_usuario']);
		//else echo 'console.log('.print_r($subas).');';*/

		//$queryuser = Usuariospujas::model()->findAll($criteria);

		//echo $queryuser;

		//echo 'console.log('.print_r($query).');';
		
		/*foreach ($query as $key => $value) {
			


			echo 'Paleta Usuario: '.' | Imagens ID: '.$value->id.' | Precio Base: '.$value->base.' | Precio Actual: '.$value->actual.'<br/>';
		}*/
	}

	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError()
	{
		if($error=Yii::app()->errorHandler->error)
		{
			if(Yii::app()->request->isAjaxRequest)
				echo $error['message'];
			else
				$this->render('error', $error);
		}
	}

	/**
	 * Displays the contact page
	 */
	public function actionContact()
	{
		$model=new ContactForm;
		if(isset($_POST['ContactForm']))
		{
			$model->attributes=$_POST['ContactForm'];
			if($model->validate())
			{
				$name='=?UTF-8?B?'.base64_encode($model->name).'?=';
				$subject='=?UTF-8?B?'.base64_encode($model->subject).'?=';
				$headers="From: $name <{$model->email}>\r\n".
					"Reply-To: {$model->email}\r\n".
					"MIME-Version: 1.0\r\n".
					"Content-type: text/plain; charset=UTF-8";

				mail(Yii::app()->params['adminEmail'],$subject,$model->body,$headers);
				Yii::app()->user->setFlash('contact','Thank you for contacting us. We will respond to you as soon as possible.');
				$this->refresh();
			}
		}
		$this->render('contact',array('model'=>$model));
	}

	/**
	 * Displays the login page
	 */
	public function actionLogin()
	{
		$model=new LoginForm;

		// if it is ajax validation request
		if(isset($_POST['ajax']) && $_POST['ajax']==='login-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

		// collect user input data
		if(isset($_POST['LoginForm']))
		{
			$model->attributes=$_POST['LoginForm'];
			// validate user input and redirect to the previous page if valid
			if($model->validate() && $model->login())
				$this->redirect(Yii::app()->user->returnUrl);
		}
		// display the login form
		$this->render('login',array('model'=>$model));
	}

	/**
	 * Logs out the current user and redirect to homepage.
	 */
	public function actionLogout()
	{
		Yii::app()->user->logout();
		$this->redirect(Yii::app()->homeUrl);
	}
}