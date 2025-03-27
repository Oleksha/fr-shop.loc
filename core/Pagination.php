<?php

namespace PHPFramework;

class Pagination
{
    /**
     * Общее количество страниц
     * @var int
     */
    protected int $countPages;
    /**
     * Текущая страница
     * @var int
     */
    protected int $currentPage;
    /**
     * Ссылка для пагинации
     * @var string
     */
    protected string $uri;

    /**
     * Конструктор класса
     * @param int $totalRecords Всего записей
     * @param int $perPage Количество записей на страницу
     * @param int $midSize Страниц показываемые дополнительно
     * @param int $maxPages Число показываемых страниц
     * @param string $tpl Шаблон вывода
     */
    public function __construct(
        protected int $totalRecords,
        protected int $perPage = PAGINATION_SETTINGS['perPage'],
        protected int $midSize = PAGINATION_SETTINGS['midSize'],
        protected int $maxPages = PAGINATION_SETTINGS['maxPages'],
        protected string $tpl = PAGINATION_SETTINGS['tpl'],
    )
    {
        $this->countPages = $this->getCountPages();
        $this->currentPage = $this->getCurrentPage();
        $this->uri = $this->getParams();
        $this->midSize = $this->getMidSize();
    }

    /**
     * Подсчитывает количество страниц для вывода
     * @return int
     */
    protected function getCountPages(): int
    {
        // Возвращаем округленное в большую сторону кол-во страниц или 1.
        return (int)ceil($this->totalRecords / $this->perPage) ?: 1;
    }

    /**
     * Получает номер текущей страницы
     * @return int
     */
    protected function getCurrentPage(): int
    {
        // Получаем из get-параметров нужную страницу или 1 если там что-то не то.
        $page = (int)request()->get('page', 1);
        if ($page < 1 || $page > $this->countPages) {
            // Если номер страниц не в действующем диапазоне - 404 ошибка
            abort();
        }
        return $page;
    }

    /**
     * Возвращает ссылку для пагинации
     * @return string
     */
    protected function getParams(): string
    {
        $url = request()->uri; // Текущий url-адрес.
        $url = parse_url($url);
        $uri = $url['path'];
        if (!empty($url['query']) && $url['query'] != '&') {
            // Преобразуем параметры в массив.
            parse_str($url['query'], $params);
            if (isset($params['page'])) {
                // Если есть параметр page - удаляем его.
                unset($params['page']);
            }
            if (!empty($params)) {
                // Если после удаления массив params не пуст формируем строку запроса.
                $uri .= '?' . http_build_query($params);
            }
        }
        return $uri;
    }

    /**
     * Возвращает количество показываемых страниц
     * @return int
     */
    protected function getMidSize(): int
    {
        return ($this->countPages <= $this->maxPages) ? $this->countPages : $this->midSize;
    }

    /**
     * Получаем стартовое значение строки для выборки из БД
     * @return int
     */
    public function getOffset(): int
    {
        return ($this->currentPage - 1) * $this->perPage;
    }

    public function getHtml()
    {
        $back = '';
        $forward = '';
        $first_page = '';
        $last_page = '';
        $pages_left = [];
        $pages_right = [];
        $current_page = $this->currentPage;
        if ($current_page > 1) {
            $back = $this->getLink($current_page - 1);
        }
        if ($current_page < $this->countPages) {
            $forward = $this->getLink($current_page + 1);
        }
        if ($current_page > $this->midSize + 1) {
            $first_page = $this->getLink(1);
        }
        if ($current_page < $this->countPages - $this->midSize) {
            $last_page = $this->getLink($this->countPages);
        }
        for ($i = $this->midSize; $i > 0; $i--) {
            if ($current_page - $i > 0) {
                $pages_left[] = [
                    'link' => $this->getLink($current_page - $i),
                    'number' => $current_page - $i,
                ];
            }
        }
        for ($i = 1; $i <= $this->midSize; $i++) {
            if ($current_page + $i <= $this->countPages) {
                $pages_right[] = [
                    'link' => $this->getLink($current_page + $i),
                    'number' => $current_page + $i,
                ];
            }
        }
        return view()->renderPartial($this->tpl, compact('back', 'forward', 'first_page', 'last_page', 'pages_left', 'pages_right', 'current_page'));
    }

    /**
     * Возвращает ссылку на конкретную страницу
     * @param int $page Номер страницы
     * @return string
     */
    protected function getLink(int $page): string
    {
        if ($page == 1) {
            return rtrim($this->uri, '?&');
        }
        if (str_contains($this->uri, '&') || str_contains($this->uri, '?')) {
            return "$this->uri&page=$page";
        } else {
            return "$this->uri?page=$page";
        }
    }

    public function __toString(): string
    {
        return $this->getHtml();
    }
}