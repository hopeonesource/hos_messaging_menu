<?php

namespace Drupal\hos_messaging_menu\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\Core\Plugin\Discovery\ContainerDeriverInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Extension\ModuleHandler;
use Symfony\Component\Yaml\Yaml;

/**
 * Class MessagingMenuLink
 *
 * @package Drupal\hos_messaging_menu\Plugin\Derivative
 */
class MessagingMenuLink extends DeriverBase implements ContainerDeriverInterface{

  /**
   * @var \Drupal\Core\Extension\ModuleHandler
   */
  protected $moduleHandler;

  /**
   * MessagingMenuLink constructor.
   *
   * @param $base_plugin_id
   * @param \Drupal\Core\Extension\ModuleHandler $moduleHandler
   */
  public function __construct($base_plugin_id, ModuleHandler $moduleHandler) {
    $this->moduleHandler = $moduleHandler;
  }

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition) {
    $links = [];

    $modules = ['hos_sms_dispatcher'];
    $module_path = drupal_get_path('module','hos_messaging_menu');
    $menuDescripContent = file_get_contents($module_path . '/hos_messaging_menu.description.yml');
    $menuDescriptions = Yaml::parse($menuDescripContent);

    foreach ($modules as $module){
      if ($this->moduleHandler->moduleExists($module)) {
        $module_path = drupal_get_path('module',$module);
        $routing_contents = file_get_contents($module_path . '/'.$module.'.routing.yml');
        $routes = Yaml::parse($routing_contents);


        foreach ($routes as $routeId => $route){
          $links[$routeId] = array(
            //'title' => $route['defaults']['_title'],
              'title' => $menuDescriptions['route_name'][$routeId]['title'],
            'route_name' => $routeId,
            'description' => $menuDescriptions['route_name'][$routeId]['description'],
            'parent' => 'hos_messaging_menu.main_menu'
          )+ $base_plugin_definition;
        }
      }
    }

    return $links;
  }
  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, $base_plugin_id) {
    return new static(
      $base_plugin_id,
      $container->get('module_handler')
    );
  }
}