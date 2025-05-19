<?php
namespace App\Core;

/**
 * Pagination Class
 * 
 * Handles pagination for database queries
 */
class Pagination {
    /**
     * @var int Total number of items
     */
    private $totalItems;
    
    /**
     * @var int Items per page
     */
    private $itemsPerPage;
    
    /**
     * @var int Current page
     */
    private $currentPage;
    
    /**
     * @var int Total number of pages
     */
    private $totalPages;
    
    /**
     * @var int Number of page links to show
     */
    private $maxPageLinks;
    
    /**
     * Constructor
     * 
     * @param int $totalItems Total number of items
     * @param int $itemsPerPage Items per page (default: 10)
     * @param int $currentPage Current page (default: 1)
     * @param int $maxPageLinks Number of page links to show (default: 5)
     */
    public function __construct($totalItems, $itemsPerPage = 10, $currentPage = 1, $maxPageLinks = 5) {
        $this->totalItems = max(0, (int) $totalItems);
        $this->itemsPerPage = max(1, (int) $itemsPerPage);
        $this->totalPages = ceil($this->totalItems / $this->itemsPerPage);
        $this->currentPage = $this->validatePageNumber($currentPage);
        $this->maxPageLinks = max(1, (int) $maxPageLinks);
    }
    
    /**
     * Validate page number
     * 
     * @param int $page Page number
     * @return int Valid page number
     */
    private function validatePageNumber($page) {
        $page = (int) $page;
        
        if ($page < 1) {
            return 1;
        }
        
        if ($page > $this->totalPages && $this->totalPages > 0) {
            return $this->totalPages;
        }
        
        return $page;
    }
    
    /**
     * Get offset for SQL LIMIT clause
     * 
     * @return int Offset
     */
    public function getOffset() {
        return ($this->currentPage - 1) * $this->itemsPerPage;
    }
    
    /**
     * Get limit for SQL LIMIT clause
     * 
     * @return int Limit
     */
    public function getLimit() {
        return $this->itemsPerPage;
    }
    
    /**
     * Get current page
     * 
     * @return int Current page
     */
    public function getCurrentPage() {
        return $this->currentPage;
    }
    
    /**
     * Get total pages
     * 
     * @return int Total pages
     */
    public function getTotalPages() {
        return $this->totalPages;
    }
    
    /**
     * Get total items
     * 
     * @return int Total items
     */
    public function getTotalItems() {
        return $this->totalItems;
    }
    
    /**
     * Get items per page
     * 
     * @return int Items per page
     */
    public function getItemsPerPage() {
        return $this->itemsPerPage;
    }
    
    /**
     * Check if there is a previous page
     * 
     * @return bool True if there is a previous page
     */
    public function hasPreviousPage() {
        return $this->currentPage > 1;
    }
    
    /**
     * Check if there is a next page
     * 
     * @return bool True if there is a next page
     */
    public function hasNextPage() {
        return $this->currentPage < $this->totalPages;
    }
    
    /**
     * Get previous page
     * 
     * @return int Previous page
     */
    public function getPreviousPage() {
        return $this->hasPreviousPage() ? $this->currentPage - 1 : 1;
    }
    
    /**
     * Get next page
     * 
     * @return int Next page
     */
    public function getNextPage() {
        return $this->hasNextPage() ? $this->currentPage + 1 : $this->totalPages;
    }
    
    /**
     * Get page range
     * 
     * @return array Page range
     */
    public function getPageRange() {
        $range = [];
        
        if ($this->totalPages <= 1) {
            return $range;
        }
        
        // Calculate start and end page
        $half = floor($this->maxPageLinks / 2);
        $start = max(1, $this->currentPage - $half);
        $end = min($this->totalPages, $start + $this->maxPageLinks - 1);
        
        // Adjust start if end is at max
        if ($end == $this->totalPages) {
            $start = max(1, $end - $this->maxPageLinks + 1);
        }
        
        // Generate range
        for ($i = $start; $i <= $end; $i++) {
            $range[] = $i;
        }
        
        return $range;
    }
    
    /**
     * Get pagination data
     * 
     * @return array Pagination data
     */
    public function getPaginationData() {
        return [
            'current_page' => $this->currentPage,
            'total_pages' => $this->totalPages,
            'total_items' => $this->totalItems,
            'items_per_page' => $this->itemsPerPage,
            'has_previous_page' => $this->hasPreviousPage(),
            'has_next_page' => $this->hasNextPage(),
            'previous_page' => $this->getPreviousPage(),
            'next_page' => $this->getNextPage(),
            'page_range' => $this->getPageRange(),
        ];
    }
    
    /**
     * Render pagination HTML
     * 
     * @param string $baseUrl Base URL for pagination links
     * @param string $queryParam Query parameter for page (default: 'page')
     * @return string Pagination HTML
     */
    public function render($baseUrl, $queryParam = 'page') {
        if ($this->totalPages <= 1) {
            return '';
        }
        
        $html = '<nav aria-label="Page navigation"><ul class="pagination">';
        
        // Previous page link
        if ($this->hasPreviousPage()) {
            $prevUrl = $this->buildUrl($baseUrl, $queryParam, $this->getPreviousPage());
            $html .= '<li class="page-item"><a class="page-link" href="' . $prevUrl . '" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>';
        } else {
            $html .= '<li class="page-item disabled"><a class="page-link" href="#" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>';
        }
        
        // Page links
        foreach ($this->getPageRange() as $page) {
            $url = $this->buildUrl($baseUrl, $queryParam, $page);
            $active = $page == $this->currentPage ? ' active' : '';
            $html .= '<li class="page-item' . $active . '"><a class="page-link" href="' . $url . '">' . $page . '</a></li>';
        }
        
        // Next page link
        if ($this->hasNextPage()) {
            $nextUrl = $this->buildUrl($baseUrl, $queryParam, $this->getNextPage());
            $html .= '<li class="page-item"><a class="page-link" href="' . $nextUrl . '" aria-label="Next"><span aria-hidden="true">&raquo;</span></a></li>';
        } else {
            $html .= '<li class="page-item disabled"><a class="page-link" href="#" aria-label="Next"><span aria-hidden="true">&raquo;</span></a></li>';
        }
        
        $html .= '</ul></nav>';
        
        return $html;
    }
    
    /**
     * Build URL for pagination
     * 
     * @param string $baseUrl Base URL
     * @param string $queryParam Query parameter for page
     * @param int $page Page number
     * @return string URL
     */
    private function buildUrl($baseUrl, $queryParam, $page) {
        $url = $baseUrl;
        
        // Add query string separator
        if (strpos($url, '?') === false) {
            $url .= '?';
        } else {
            $url .= '&';
        }
        
        // Add page parameter
        $url .= $queryParam . '=' . $page;
        
        return $url;
    }
}
