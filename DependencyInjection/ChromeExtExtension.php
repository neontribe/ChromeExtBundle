<?php
namespace KimaiPlugin\ChromeExtBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class ChromeExtExtension extends Extension {

  public function load(array $configs, ContainerBuilder $container) {
    try {
      $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
      $loader->load('services.yaml');
    } catch (\Exception $e) {
      echo '[ChromeExtExtension] invalid services config found: ' . $e->getMessage();
    }
  }
}
