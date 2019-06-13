<?php
namespace KimaiPlugin\ChromeExtBundle\Entity;

use App\Entity\Project;
use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @ORM\Table(name="kimai2_extproject")
 * @ORM\Entity(repositoryClass="KimaiPlugin\ChromeExtBundle\Repository\ExtProjectRepository")
 */
class ExtProject {

  /**
   *
   * @ORM\Id()
   * @ORM\GeneratedValue()
   * @ORM\Column(type="integer")
   */
  private $id;

  /**
   *
   * @var Project
   *
   * @ORM\OneToOne(targetEntity="App\Entity\Project", inversedBy="activities")
   */
  private $project;

  /**
   *
   * @var ExtIssue[]
   *
   * @ORM\OneToMany(targetEntity="KimaiPlugin\ChromeExtBundle\Entity\ExtIssue", mappedBy="project")
   */
  private $issues;

  /**
   *
   * @ORM\Column(type="string", length=255)
   */
  private $uuid;

  public function getId(): ?int {
    return $this->id;
  }

  public function getProject(): ?Project {
    return $this->project;
  }

  public function setProject(Project $project): self {
    $this->project = $project;

    return $this;
  }

  public function getUuid(): ?string {
    return $this->uuid;
  }

  public function setUuid(string $uuid): self {
    $this->uuid = $uuid;

    return $this;
  }

  /**
   *
   * @return ExtIssue[]
   */
  public function getIssues() {
      return $this->issues;
  }

  /**
   * @param ExtIssue $extIssue
   */
  public function addIssue(ExtIssue $extIssue)
  {
      if ($this->issues->contains($extIssue)) {
          return;
      }

      $this->issues->add($extIssue);
      $extIssue->setProject($this);
  }

  /**
   * @param ExtIssue $timesheet
   */
  public function removeIssue(ExtIssue $extIssue)
  {
      if (!$this->issues->contains($extIssue)) {
          return;
      }

      $this->issues->removeElement($extIssue);
  }

}
