<?php

/* @var $this \yii\web\View */

/* @var $content string */

use backend\assets\AppAsset;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use common\widgets\LanguageChoice;
use common\widgets\Alert;
use common\models\User;
use modules\buildingprocess\models\ProcessTemplate;
use modules\module\models\Module;
use modules\i18n\models\Language;
use backend\assets\ClientAsset;

AppAsset::register($this);
ClientAsset::register($this);

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
  <meta charset="<?= Yii::$app->charset ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
  <title><?= Html::encode($this->title) ?></title>
    <?php $this->head(); ?>
</head>
<body class="open-sidebar">
<?php $this->beginBody(); ?>
<?php $user = User::findOne(Yii::$app->user->id); ?>
<header>
  <div class="sidebar-toggle">
    <div class="logo-link">
        <?= Html::a('', [Yii::$app->homeUrl]) ?>
    </div>
    <div class="toggle"></div>
  </div>
  <div class="logo">
      <?= Html::a(Yii::t('app', 'Admin Panel')) ?>
  </div>
  <div class="menu">
    <div class="menu-item lang"><?= LanguageChoice::widget([
            'languages' => Language::getList(),
            'currentLanguage' => Yii::$app->session->get('language', 'en'),
        ]) ?></div>
      <?php if (! \Yii::$app->user->isGuest) : ?>
        <div class="menu-item auth auth-logout">
            <?= Html::a(
                Html::tag('span', '', [
                    'class' => '',
                    'title' => \Yii::t('app', 'Logout')
                ]),
                '/auth/logout'
            ); ?>
        </div>
      <?php endif; ?>
  </div>
    <?php if (! \Yii::$app->user->isGuest) : ?>
      <div class="user">
        <div class="user-name">
            <?= Html::a(
                \Yii::$app->user->identity->first_name . ' ' . \Yii::$app->user->identity->last_name,
                '/user/update?id=' . \Yii::$app->user->id . '&params[page]=1'
            ); ?>
        </div>
        <div class="user-avatar">
            <?= Html::a(
                Html::img($user->imageThumbnailUrl, ['class' => 'img-thumbnail avatar']),
                '/user/update?id=' . \Yii::$app->user->id . '&params[page]=1'
            ); ?>
        </div>
        <div class="clearfix"></div>
      </div>
    <?php endif; ?>
  <div class="clearfix"></div>
</header>
<main>
    <?php if (! \Yii::$app->user->isGuest) : ?>
      <div class="sidebar">
        <div class="scroller">
          <?php NavBar::begin([
              'options' => [
                  'class' => 'sidebar-body ',
              ],
              'renderInnerContainer' => false,
          ]); ?>
          <?php
          $modules = Module::getAvailableModules();
          $buildingProcesses = ProcessTemplate::getAvailableDisplay();

          $menuItems = [];

          $menuItems = [
              [
                  'label' => '<span class="icon icon-users"></span><span class="sidebar-title"> ' .
                             Yii::t('app', 'Users') . '</span>',
                  'items' => User::getMenu(),
                  'options' => (strpos(Url::toRoute(''),'user') ||
                                Yii::$app->controller->module->id === 'position' ||
                                Yii::$app->controller->module->id === 'department') ?
                      (['class' => 'active']) : (['class' => '']),
                  'url' => ['/user/index']
              ],
          ];

          $index = 0;
          foreach ($modules as $module) {
              $systemModule = \Yii::$app->getModule($module->slug);
              $menu = array();
              if (! empty($systemModule->params['admin_modules'])) {
                  $menu = $systemModule->params['admin_modules'];
              }

              foreach ($menu as &$menuItem) {
                  if (! empty($menuItem['url'][0])) {
                      if ($menuItem['url'][0] === Url::toRoute('')) {
                          $menuItem['options'] = ['class' => 'active'];
                      }
                  }
              }
              unset($menuItem);

              $active = false;

              if (Yii::$app->controller->module->id === $module->slug &&
                  Url::toRoute('') != '/buildingprocess/deal/index') {
                  $active = true;
              } elseif ($module->slug === 'config' && (Yii::$app->controller->module->id === 'holiday' ||
                                                       Yii::$app->controller->module->id === 'holidayconfig' ||
                                                       Yii::$app->controller->module->id === 'registration' ||
                                                       (Url::toRoute('') === '/role' &&
                                                       Yii::$app->controller->module->id === 'app-backend'))) {
                $active = true;
              }

              $menuItems[] = [
                  'label' => '<span class="icon icon-' . $module->slug . '"></span><span class="sidebar-title"> ' .
                             Yii::t('app', $module->name) . '</span>',
                  'items' => $menu,
                  'options' => $active ? ['class' => 'active'] : ['class' => ''],
                  'url' => ['/' . $module->slug . '/index']
              ];

              $index++;
          }

          foreach ($buildingProcesses as $buildingProcess) {

              $menuItems[] = [
                  'label' => '<span class="icon icon-process"></span><span class="sidebar-title"> ' .
                             Yii::t('app', $buildingProcess->type) . '</span>',
                  'items' => [],
                  'options' => [],
                  'url' => ['/buildingprocess/deal/index', 'type' => $buildingProcess->type],
              ];
          }
          ?>
          <?= Nav::widget([
              'options' => ['class' => 'nav sidebar-menu'],
              'dropDownCaret' => '<span class="caret"></span>',
              'items' => $menuItems,
              'encodeLabels' => false,
          ]); ?>
          <?php NavBar::end(); ?>
        </div>
      </div>
    <?php endif; ?>
  <div class="content">
    <span>
      <?= Alert::widget() ?>
      <?= $content ?>
    </span>
  </div>
</main>
<footer class="footer <?= \Yii::$app->user->isGuest ? 'no-sidebar' : '' ?>">
  <div class="container">
    <p class="pull-left">&copy; IT-Master <?= date('Y') ?></p>
    <p class="pull-right">
        <?= Yii::t('app', 'Created by') ?> <a href="https://hire.itmaster-soft.com/" rel="external"
                                              target="_blank">IT-Master</a></p>
  </div>

  <div class="up"><i class="glyphicon glyphicon-chevron-up"></i></div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
