<?php declare(strict_types = 1);

namespace App\PageModule\Model;

class PageMapping
{

	public const TABLE_NAME = 'page';
	public const COLUMN_ID = 'id';
	public const COLUMN_INCLUDE_HEADER = 'include_header';
	public const COLUMN_INCLUDE_FOOTER = 'include_footer';
	public const COLUMN_MODULE = 'module';
	public const COLUMN_PRESENTER = 'presenter';
	public const COLUMN_ACTION = 'action';
	public const COLUMN_UID = 'uid';

	public const TEXT_TABLE_NAME = 'page_text';
	public const COLUMN_TEXT_PAGE = 'page';
	public const COLUMN_TEXT_LANG = 'language';
	public const COLUMN_TEXT_TITLE = 'title';
	public const COLUMN_TEXT_PARENT_PAGE = 'parent_page';
	public const COLUMN_TEXT_SORT = 'sort';
	public const COLUMN_TEXT_ALIAS = 'alias';

}
