<?php

namespace Drupal\Core\Template\Loader;

use Drupal\Component\Discovery\YamlDirectoryDiscovery;
use Drupal\Core\Render\Component\Exception\ComponentNotFoundException;
use Drupal\Core\Theme\ComponentPluginManager;
use Drupal\Core\Utility\Error;
use Psr\Log\LoggerInterface;
use Twig\Error\LoaderError;
use Twig\Loader\LoaderInterface;
use Twig\Source;

/**
 * Lets you load templates using the component ID.
 */
class ComponentLoader implements LoaderInterface {

  /**
   * Constructs a new ComponentLoader object.
   *
   * @param \Drupal\Core\Theme\ComponentPluginManager $pluginManager
   *   The plugin manager.
   * @param \Psr\Log\LoggerInterface $logger
   *   The logger.
   */
  public function __construct(
    protected ComponentPluginManager $pluginManager,
    protected LoggerInterface $logger,
  ) {}

  /**
   * Finds a template in the file system based on the template name.
   *
   * @param string $name
   *   The template name.
   * @param bool $throw
   *   TRUE to throw an exception when the component is not found. FALSE to
   *   return NULL if the component cannot be found.
   *
   * @return string|null
   *   The path to the component.
   *
   * @throws \Twig\Error\LoaderError
   *   Thrown if a template matching $name cannot be found and $throw is TRUE.
   */
  protected function findTemplate(string $name, bool $throw = TRUE): ?string {
    $path = $name;
    try {
      $component = $this->pluginManager->find($name);
      $path = $component->getTemplatePath();
    }
    catch (ComponentNotFoundException $e) {
      if ($throw) {
        throw new LoaderError($e->getMessage(), $e->getCode(), $e);
      }
    }
    if ($path || !$throw) {
      return $path;
    }

    throw new LoaderError(sprintf('Unable to find template "%s" in the components registry.', $name));
  }

  /**
   * {@inheritdoc}
   */
  public function exists($name): bool {
    if (!preg_match('/^[a-zA-Z][a-zA-Z0-9:_-]*[a-zA-Z0-9]?$/', $name)) {
      return FALSE;
    }
    try {
      $this->pluginManager->find($name);
      return TRUE;
    }
    catch (ComponentNotFoundException $e) {
      Error::logException($this->logger, $e);
      return FALSE;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getSourceContext($name): Source {
    try {
      $component = $this->pluginManager->find($name);
      $path = $component->getTemplatePath();
    }
    catch (ComponentNotFoundException) {
      return new Source('', $name, '');
    }
    $original_code = file_get_contents($path);
    return new Source($original_code, $name, $path);
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheKey($name): string {
    try {
      $component = $this->pluginManager->find($name);
    }
    catch (ComponentNotFoundException) {
      throw new LoaderError('Unable to find component');
    }
    return implode('--', array_filter([
      'components',
      $name,
      $component->getPluginDefinition()['provider'] ?? '',
    ]));
  }

  /**
   * {@inheritdoc}
   */
  public function isFresh(string $name, int $time): bool {
    $file_is_fresh = static fn(string $path) => filemtime($path) < $time;
    try {
      $component = $this->pluginManager->find($name);
    }
    catch (ComponentNotFoundException) {
      throw new LoaderError('Unable to find component');
    }
    $metadata_path = $component->getPluginDefinition()[YamlDirectoryDiscovery::FILE_KEY];
    return $file_is_fresh($component->getTemplatePath()) && $file_is_fresh($metadata_path);
  }

}
