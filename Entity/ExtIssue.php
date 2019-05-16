<?php
namespace KimaiPlugin\ChromeExtBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Timesheet;

/**
 *
 * @ORM\Table(name="kimai2_extissue")
 * @ORM\Entity(repositoryClass="KimaiPlugin\ChromeExtBundle\Repository\ExtIssueRepository")
 */
class ExtIssue {

  /**
   *
   * @ORM\Id()
   * @ORM\GeneratedValue()
   * @ORM\Column(type="integer")
   */
  private $id;

  /**
   *
   * @var ExtProject
   *
   * @ORM\ManyToOne(targetEntity="KimaiPlugin\ChromeExtBundle\Entity\ExtProject", inversedBy="issues")
   * @ORM\JoinColumn(onDelete="CASCADE")
   */
  private $project;

  /**
   *
   * @var Timesheet
   *
   * @ORM\ManyToMany(targetEntity="App\Entity\Timesheet")
   * @ORM\JoinTable(name="kimai2_extissues_timesheets",
   *      joinColumns={@ORM\JoinColumn(name="issue_id", referencedColumnName="id")},
   *      inverseJoinColumns={@ORM\JoinColumn(name="timesheet_id", referencedColumnName="id", unique=true)}
   *      )
   */
  private $timesheets;

  /**
   *
   * @ORM\Column(type="string", length=255)
   */
  private $uuid;

  public function getId(): ?int {
    return $this->id;
  }

  public function getProject(): ?ExtProject {
    return $this->project;
  }

  public function setProject(ExtProject $project): self {
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
   * @return \App\Entity\Timesheet
   */
  public function getTimesheets() {
    return $this->timesheets;
  }

  /**
   *
   * @param \App\Entity\Timesheet[] $timesheets
   * @return ExtIssue
   */
  public function setTimesheets($timesheets): self {
    $this->timesheets = $timesheets;

    return $this;
  }

  public function addTimesheet(Timesheet $timesheet): self {
    $this->timesheets[] = $timesheet;

    return $this;
  }
}
