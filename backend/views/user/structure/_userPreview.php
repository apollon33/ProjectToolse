<?php
use yii\helpers\Url;
use common\models\User;
use yii\helpers\Html;

extract($params);
?>

<div class="container wrapper_profile">
	<br />
	<br />
	<div class="row">

		<div>
			<div class="content_avatar" >
				<img src="<?= $node->imageUrl; ?>" alt="<?= $node->imageUrl; ?>">
				<h4 class="header_contact"><?= Yii::t('app','Contact me at')?></h4>
			</div>
			<div class="content_right">
				<h1><?= $node->fullName ?></h1>
				<h2><?= !empty($node->logPosition->name) ? $node->logPosition->name : null ;?></h2>
				<p>since <?= Yii::$app->formatter->asDate($node->date_receipt,'php:Y F') ?> - present
			</div>
			<div class="content_top">
				<div class="content_career">
					<h2><?= Yii::t('app','Career History')?>:</h2><div>
						<?php foreach ($logPosition as $key => $position):?>

							<h5><?= !empty($position->position->name) ? $position->position->name : null ;?></h5>
							<div>
								<?= Yii::$app->formatter->asDate($position->created_at, 'php:Y F') ?> -
								<?= $key ? Yii::$app->formatter->asDate($logPosition[$key - 1]->created_at, 'php:Y F') : Yii::t('app', 'present'); ?>
							</div>

						<?php endforeach;?>
					</div>
				</div>
				<?php if(!empty($node->slogan)):?>
					<div>
						<img src="/images/u134.png" alt="/images/u134.png">
						<?= Html::encode($node->slogan); ?>
					</div>
				<?php endif;?>
				<?php if(!empty($node->birthday) && !$node->secret_birthday):?>
					<div>
						<img src="/images/u132.png" alt="/images/u132.png">
						<span><?= Yii::$app->formatter->asDate($node->birthday,'php:F d')?></span>
					</div>
				<?php endif;?>
				<?php if(!empty($node->like)):?>
					<div>
						<img src="/images/u124.png" alt="/images/u124.png">
						<?= Html::encode($node->like); ?>
					</div>
				<?php endif;?>
				<?php if(!empty($node->dislike)):?>
					<div >
						<img src="/images/u122.png" alt="/images/u122.png">
						<?= Html::encode($node->dislike); ?>
					</div>
				<?php endif;?>
			</div>
		</div>
		<div class="contact_profile col-md-12">
			<div>
				<span><img src="/images/u118.png" alt="/images/u122.png"></span>
				<?= Html::input('text', 'email', $node->email, ['class' => 'input_profile','disabled'=>true]) ?>
			</div>
			<div>
				<span><img src="/images/u128.png" alt="/images/u128.png"></span>
				<?= Html::input('text', 'phone', $node->phone, ['class' => 'input_profile','disabled'=>true]) ?>
			</div>
			<?php if(!empty($node->skype)) : ?>
				<div>
					<span><img src="/images/u130.png" alt="/images/u130.png"></span>
					<?= Html::input('text', 'skype', Html::encode($node->skype), ['class' => 'input_profile','disabled'=>true]) ?>
				</div>
			<?php endif; ?>
			<?php if(!empty( $node->facebook)) : ?>
				<div>
					<span><img src="/images/u120.png" alt="/images/u120.png"></span>
					<?php if(!empty($node->facebook)) :?>
						<a href="https://www.facebook.com/profile.php?id=<?= $node->facebook ?>" target="true">facebook.com</a>
					<?php endif;?>
				</div>
			<?php endif; ?>
			<?php if(!empty( $node->linkedin)) : ?>
				<div>
					<span><img src="/images/u126.png" alt="/images/u126.png"></span>
					<?php if(!empty($node->linkedin)) :?>
						<a href="https://www.linkedin.com/in/<?= $node->linkedin ?>" target="true">linkedin.com</a>
					<?php endif;?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</div>