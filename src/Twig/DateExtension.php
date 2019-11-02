<?php


namespace App\Twig;


use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class DateExtension extends AbstractExtension
{
    /**
     * @return array|TwigFilter[]
     */
    public function getFilters()
    {
        return [
            new TwigFilter("addedDate", [$this, "addedDate"])
        ];
    }

    public function addedDate(\DateTime $createdAt)
    {

        if ($createdAt > new \DateTime("-1 min")) {
            return 'Created: just now';
        }

        if ($createdAt > new \DateTime("-1 hour")) {
            if($createdAt->diff(new \DateTime())->i == 1) {
                return $createdAt->diff(new \DateTime())->i . ' minute ago.';
            }
            return $createdAt->diff(new \DateTime())->i . ' minutes ago.';
        }

        if ($createdAt > new \DateTime("-1 day")) {
            if($createdAt->diff(new \DateTime())->h == 1) {
                return 'Created: '. $createdAt->diff(new \DateTime())->h . ' hour ago.';
            }
            return 'Created: '. $createdAt->diff(new \DateTime())->h . ' hours ago.';
        }

        return 'Created: '. $createdAt->diff(new \DateTime())->days . ' days ago.';


    }
}