<?php
namespace app\common;

use think\Paginator;

/**
 * 自定义分页驱动 — Bootstrap风格（现代美观）
 * 支持分页省略号与禁用状态
 */
class Bootstrap extends Paginator
{
    /**
     * 渲染分页HTML
     * @return string
     */
    public function render(): string
    {
        // 无数据时不显示分页
        if ($this->isEmpty()) {
            return '';
        }

        $html = '<nav aria-label="Page navigation example"><ul class="pagination justify-content-end">';

        // 上一页
        if ($this->currentPage() > 1) {
            $html .= $this->getPreviousButton('&laquo;');
        } else {
            $html .= '<li class="page-item disabled"><span class="page-link">&laquo;</span></li>';
        }

        // 中间页码（带省略号）
        $html .= $this->getLinks();

        // 下一页
        if ($this->hasMore) {
            $html .= $this->getNextButton('&raquo;');
        } else {
            $html .= '<li class="page-item disabled"><span class="page-link">&raquo;</span></li>';
        }

        $html .= '</ul></nav>';

        return $html;
    }

    /**
     * 上一页按钮
     */
    protected function getPreviousButton(string $text = '&laquo;'): string
    {
        $url = $this->url($this->currentPage() - 1);
        return '<li class="page-item"><a class="page-link" href="' . htmlentities($url) . '" aria-label="Previous">' . $text . '</a></li>';
    }

    /**
     * 下一页按钮
     */
    protected function getNextButton(string $text = '&raquo;'): string
    {
        $url = $this->url($this->currentPage() + 1);
        return '<li class="page-item"><a class="page-link" href="' . htmlentities($url) . '" aria-label="Next">' . $text . '</a></li>';
    }

    /**
     * 页码链接生成（带省略号）
     */
    protected function getLinks(): string
    {
        $html = '';

        $side = 2; // 当前页前后显示的页码数
        $window = $side * 2;

        if ($this->lastPage() < $window + 6) {
            $range = range(1, $this->lastPage());
        } else {
            if ($this->currentPage() <= $window) {
                $range = range(1, $window + 2);
                $range[] = '...';
                $range[] = $this->lastPage();
            } elseif ($this->currentPage() > ($this->lastPage() - $window)) {
                $range = [1, '...'];
                $range = array_merge($range, range($this->lastPage() - ($window + 2), $this->lastPage()));
            } else {
                $range = [1, '...'];
                $range = array_merge($range, range($this->currentPage() - $side, $this->currentPage() + $side));
                $range[] = '...';
                $range[] = $this->lastPage();
            }
        }

        foreach ($range as $page) {
            if ($page === '...') {
                $html .= '<li class="page-item disabled"><span class="page-link">…</span></li>';
                continue;
            }

            if ($page == $this->currentPage()) {
                $html .= '<li class="page-item active" aria-current="page"><span class="page-link">' . $page . '</span></li>';
            } else {
                $url = $this->url($page);
                $html .= '<li class="page-item"><a class="page-link" href="' . htmlentities($url) . '">' . $page . '</a></li>';
            }
        }

        return $html;
    }

    /**
     * 将分页结果转为数组（适合API）
     */
    public function toArray(): array
    {
        return [
            'total' => $this->total(),
            'per_page' => $this->listRows(),
            'current_page' => $this->currentPage(),
            'last_page' => $this->lastPage(),
            'data' => $this->items->toArray(),
        ];
    }
}
