<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 20.08.18
 * Time: 09:41
 */

namespace Jinya\Framework\Events\SegmentPages;

use Jinya\Entity\SegmentPage\SegmentPage;
use Jinya\Framework\Events\Common\CancellableEvent;

class SegmentPageEvent extends CancellableEvent
{
    public const PRE_SAVE = 'SegmentPagePreSave';

    public const POST_SAVE = 'SegmentPagePostSave';

    public const PRE_GET = 'SegmentPagePreGet';

    public const POST_GET = 'SegmentPagePostGet';

    public const PRE_DELETE = 'SegmentPagePreDelete';

    public const POST_DELETE = 'SegmentPagePostDelete';

    /** @var SegmentPage */
    private $segmentPage;

    /** @var string */
    private $slug;

    /**
     * SegmentPageEvent constructor.
     * @param SegmentPage $segmentPage
     * @param string $slug
     */
    public function __construct(?SegmentPage $segmentPage, string $slug)
    {
        $this->segmentPage = $segmentPage;
        $this->slug = $slug;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @return SegmentPage
     */
    public function getSegmentPage(): ?SegmentPage
    {
        return $this->segmentPage;
    }
}