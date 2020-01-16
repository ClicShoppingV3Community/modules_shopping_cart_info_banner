<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4
 *  @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

  use ClicShopping\Apps\Marketing\BannerManager\Classes\Shop\Banner;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;

  class ms_shopping_cart_info_banner {
    public $code;
    public $group;
    public $title;
    public $description;
    public $sort_order;
    public $enabled = false;

    public function __construct() {
      $this->code = get_class($this);
      $this->group = basename(__DIR__);

      $this->title = CLICSHOPPING::getDef('module_shopping_cart_info_banner');
      $this->description = CLICSHOPPING::getDef('module_shopping_cart_info_banner_description');

      if (defined('MODULE_SHOPPING_CART_INFO_BANNER_STATUS')) {
        $this->sort_order = MODULE_SHOPPING_CART_INFO_BANNER_SORT_ORDER;
        $this->enabled = (MODULE_SHOPPING_CART_INFO_BANNER_STATUS == 'True');
      }
     }

    public function execute() {

      $CLICSHOPPING_ShoppingCart = Registry::get('ShoppingCart');
      $CLICSHOPPING_Template = Registry::get('Template');
      $CLICSHOPPING_Service = Registry::get('Service');
      $CLICSHOPPING_Banner = Registry::get('Banner');

      if (isset($_GET['Cart']) && $CLICSHOPPING_ShoppingCart->getCountContents() > 0) {
        $shopping_cart_process_order_banner = '';

        if ($CLICSHOPPING_Service->isStarted('Banner') ) {
          if ($banner = $CLICSHOPPING_Banner->bannerExists('dynamic',  MODULE_SHOPPING_CART_INFO_BANNER_BANNER_GROUP)) {
            $shopping_cart_process_order_banner = $CLICSHOPPING_Banner->displayBanner('dynamic', $banner) . '<br /><br />';
          }
        }

        $content_width = (int)MODULE_SHOPPING_CART_INFO_BANNER_CONTENT_WIDTH;
        $position = MODULE_SHOPPING_CART_INFO_BANNER_POSITION;

         $shopping_cart_information_order_process = '  <!-- Shopping_cart start information order process -->'. "\n";

         ob_start();
         require_once($CLICSHOPPING_Template->getTemplateModules($this->group . '/content/shopping_cart_info_banner'));

         $shopping_cart_information_order_process .= ob_get_clean();

         $shopping_cart_information_order_process .= '<!--  end shopping_cart start information order process -->' . "\n";

         $CLICSHOPPING_Template->addBlock($shopping_cart_information_order_process, $this->group);
      }
    } // public function execute

    public function isEnabled() {
      return $this->enabled;
    }

    public function check() {
      return defined('MODULE_SHOPPING_CART_INFO_BANNER_STATUS');
    }

    public function install() {
      $CLICSHOPPING_Db = Registry::get('Db');

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Do you want to enable this module ?',
          'configuration_key' => 'MODULE_SHOPPING_CART_INFO_BANNER_STATUS',
          'configuration_value' => 'True',
          'configuration_description' => 'Do you want to enable this module in your shop ?',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Please select the width of the display?',
          'configuration_key' => 'MODULE_SHOPPING_CART_INFO_BANNER_CONTENT_WIDTH',
          'configuration_value' => '12',
          'configuration_description' => 'Please enter a number between 1 and 12',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_content_module_width_pull_down',
          'date_added' => 'now()'
        ]
      );


      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Please indicate the group where the banner belongs',
          'configuration_key' => 'MODULE_SHOPPING_CART_INFO_BANNER_BANNER_GROUP',
          'configuration_value' => SITE_THEMA.'_shopping_cart',
          'configuration_description' => 'Veuillez indiquer le groupe d\'appartenance de la bannière<br /><br /><strong>Note :</strong><br /><i>Le groupe sera à indiquer lors de la cré;ation de la bannière dans la section Marketing / Gestion des bannières</i>',
          'configuration_group_id' => '6',
          'sort_order' => '4',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Where do you want to display the module?',
          'configuration_key' => 'MODULE_SHOPPING_CART_INFO_BANNER_POSITION',
          'configuration_value' => 'none',
          'configuration_description' => 'Displays the module to the left or to the right ',
          'configuration_group_id' => '6',
          'sort_order' => '2',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'float-md-right\', \'float-md-left\' \'float-md-none\'))',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Sort order',
          'configuration_key' => 'MODULE_SHOPPING_CART_INFO_BANNER_SORT_ORDER',
          'configuration_value' => '150',
          'configuration_description' => 'Sort order of display. Lowest is displayed first. The sort order must be different on every module',
          'configuration_group_id' => '6',
          'sort_order' => '4',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );

      return $CLICSHOPPING_Db->save('configuration', ['configuration_value' => '1'],
        ['configuration_key' => 'WEBSITE_MODULE_INSTALLED']
      );
    }

    public function remove() {
      return Registry::get('Db')->exec('delete from :table_configuration where configuration_key in ("' . implode('", "', $this->keys()) . '")');
    }

    public function keys() {
      return array (
        'MODULE_SHOPPING_CART_INFO_BANNER_STATUS',
        'MODULE_SHOPPING_CART_INFO_BANNER_CONTENT_WIDTH',
        'MODULE_SHOPPING_CART_INFO_BANNER_POSITION',
        'MODULE_SHOPPING_CART_INFO_BANNER_BANNER_GROUP',
        'MODULE_SHOPPING_CART_INFO_BANNER_SORT_ORDER'
      );
    }
  }
