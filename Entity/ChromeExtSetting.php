<?php

namespace KimaiPlugin\ChromeExtBundle\Entity;

class ChromeExtSetting
{
    private $durationOnly = true;
    private $showTags = false;
    private $showFixedRate = false;
    private $showHourlyRate = false;

    /**
     * @return bool
     */
    public function isDurationOnly(): bool
    {
        return $this->durationOnly;
    }

    /**
     * @param bool $durationOnly
     * @return ChromeExtSetting
     */
    public function setDurationOnly(bool $durationOnly): ChromeExtSetting
    {
        $this->durationOnly = $durationOnly;
        return $this;
    }

    /**
     * @return bool
     */
    public function isShowTags(): bool
    {
        return $this->showTags;
    }

    /**
     * @param bool $showTags
     * @return ChromeExtSetting
     */
    public function setShowTags(bool $showTags): ChromeExtSetting
    {
        $this->showTags = $showTags;
        return $this;
    }

    /**
     * @return bool
     */
    public function isShowFixedRate(): bool
    {
        return $this->showFixedRate;
    }

    /**
     * @param bool $showFixedRate
     * @return ChromeExtSetting
     */
    public function setShowFixedRate(bool $showFixedRate): ChromeExtSetting
    {
        $this->showFixedRate = $showFixedRate;
        return $this;
    }

    /**
     * @return bool
     */
    public function isShowHourlyRate(): bool
    {
        return $this->showHourlyRate;
    }

    /**
     * @param bool $showHourlyRate
     * @return ChromeExtSetting
     */
    public function setShowHourlyRate(bool $showHourlyRate): ChromeExtSetting
    {
        $this->showHourlyRate = $showHourlyRate;
        return $this;
    }


}
