<?php

namespace SEOUtils\XF\Sitemap;

class Thread extends XFCP_Thread
{
    public function getEntry($record)
    {
        /** @var \XF\Entity\Thread $record */

        $result = parent::getEntry($record);

        if (is_array($result) || !$this->app->options()->SEOUtilsIncludeAllPages) {
            return $result;
        }

        $perPage = max(20, intval($this->app->options()->messagesPerPage));
        $total = max(0, intval($record->reply_count + 1));
        $totalPages = ceil($total / $perPage);
        
        if ($totalPages <= 1) {
            return $result;
        }
        
        $resultWithPages = [$result];

        // We create an \XF\Sitemap\Entry for each page of a thread.
        for($page = 2; $page <= $totalPages; $page++)
        {
            $url = $this->app->router('public')->buildLink('canonical:threads', $record, ['page' => $page]);
            $resultWithPages[] = \XF\Sitemap\Entry::create($url, [
                'lastmod' => $record->last_post_date
            ]);
        }

        return $resultWithPages;
    }
}