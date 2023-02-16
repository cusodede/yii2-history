<?php
declare(strict_types = 1);

/**
 * @var View $this
 * @var string $content
 */

use yii\helpers\Html;
use yii\web\View;

?>
<!DOCTYPE html>
<?php $this->beginPage(); ?>
<html lang="<?= Yii::$app->language ?>">
<head>
	<meta charset="<?= Yii::$app->charset ?>"/>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, user-scalable=no, minimal-ui">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="msapplication-tap-highlight" content="no">
	<?= Html::csrfMetaTags() ?>
	<title><?= $this->title ?></title>
	<?php $this->head(); ?>
</head>

<body class="mod-bg-1 header-function-fixed nav-function-fixed mod-nav-link mod-skin-light">
<?php $this->beginBody(); ?>

<?= $content ?>
<?php $this->endBody(); ?>
</body>
<?php $this->endPage(); ?>
</html>