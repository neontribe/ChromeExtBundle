<?php
namespace KimaiPlugin\ChromeExtBundle\Repository;

use KimaiPlugin\ChromeExtBundle\Entity\ChromeExtSetting;
use KimaiPlugin\CustomCSSBundle\Entity\CustomCss;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class ChromeExtRepository
{
    /**
     * @var string
     */
    protected $settingsFile;

    /**
     * @param string $dataDirectory
     */
    public function __construct(string $dataDirectory)
    {
        $this->settingsFile = $dataDirectory . '/chrome-ext';
    }

    /**
     * @param ChromeExtSetting $entity
     * @return bool
     * @throws \Exception
     */
    public function saveConfig(ChromeExtSetting $settings)
    {
        if (file_exists($this->settingsFile) && !is_writable($this->settingsFile)) {
            throw new \Exception('Settings file is not writable: ' . $this->settingsFile);
        }
        if (false === file_put_contents($this->settingsFile, $this->toJson($settings))) {
            throw new \Exception('Failed saving custom css rules to file: ' . $this->settingsFile);
        }
        return true;
    }
    /**
     * @return ChromeExtSetting
     */
    public function getConfig()
    {
        $entity = new ChromeExtSetting();
        if (file_exists($this->settingsFile) && is_readable($this->settingsFile)) {
            $entity = $this->fromJson(file_get_contents($this->settingsFile));
        }
        return $entity;
    }

    /**
     * @param string $json
     * @return ChromeExtSetting
     */
    public function fromJson(string $json) {
        $data = json_decode($json, true);
        $entity = new ChromeExtSetting();

        if (array_key_exists('durationOnly', $data)) {
            $entity->setDurationOnly($data['durationOnly']);
        }

        if (array_key_exists('showTags', $data)) {
            $entity->setShowTags($data['showTags']);
        }

        if (array_key_exists('showFixedRate', $data)) {
            $entity->setShowFixedRate($data['showFixedRate']);
        }

        if (array_key_exists('showHourlyRate', $data)) {
            $entity->setShowHourlyRate($data['showHourlyRate']);
        }

        return $entity;
    }

    /**
     * @param ChromeExtSetting $settings
     * @return false|string
     */
    public function toJson(ChromeExtSetting $settings) {
        $data = [
            'durationOnly' => $settings->isDurationOnly(),
            'showTags' => $settings->isShowTags(),
            'showFixedRate' => $settings->isShowFixedRate(),
            'showHourlyRate' => $settings->isShowHourlyRate(),
        ];
        return json_encode($data, JSON_PRETTY_PRINT);
    }

}