<?php


include __DIR__ . '/../../phpdoc-parser/vendor/autoload.php';


$hooksParser = new HooksParser( [
	'scanDirectory'     => dirname( __FILE__ ) . '/../../anycomment',
	'ignoreDirectories' => [
		'vendor',
		'languages'
	]
] );

$parsedItems = $hooksParser->parse();

$hooksDocumentation = new HookDocumentation( $parsedItems );
$hooksDocumentation->setSaveLocation(dirname( __FILE__ ) . '/../docs/hooks.md');
$hooksDocumentation->write();

/**
 * Class HookDocumentation helps to generate automatic .md doc about hooks in the plugin.
 */
class HookDocumentation {

	/**
	 * @var HookDto[]
	 */
	private $_hooks;

	/**
	 * @var string Path to the place where to save .md file.
	 */
	private $_saveLocation;

	/**
	 * @var string Generated content of markdown document.
	 */
	private $_markdownContent = '';

	/**
	 * HookDocumentation constructor.
	 *
	 * @param HookDto[] $hooks
	 */
	public function __construct( $hooks ) {
		$this->_hooks = $hooks;
	}

	/**
	 * @param HookDto[] $hooks
	 */
	public function setHooks( $hooks ) {
		$this->_hooks = $hooks;
	}

	/**
	 * @param string $saveLocation
	 */
	public function setSaveLocation( $saveLocation ) {
		$this->_saveLocation = $saveLocation;
	}

	/**
	 * Writes documentation.
	 *
	 * @return bool
	 * @throws Exception
	 */
	public function write() {

		foreach ( $this->_hooks as $item ) {
			$this->generateSingleHook( $item );
		}

		return $this->saveContent();
	}

	/**
	 * Generates hook function.
	 *
	 * @param HookDto $hook
	 */
	protected function generateFunction( $hook ) {
		// Function execution
		$function = '';

		switch ( $hook->type ) {
			case 'filter':
				$function = 'apply_filters(';
				break;
			case 'action':
				$function = 'do_action(';
				break;
			default:
		}

		$function .= sprintf( '"%s"', $hook->name );

		if ( empty( $hook->docBlock->tags ) ) {
			$function .= ')';
		} else {
			/**
			 * @var HookTagDto $tag
			 */
			foreach ( $hook->docBlock->tags as $tag ) {
				if(empty($tag->variable)) {
					continue;
				}

				if ( empty( $tag->types ) ) {
					$function .= ', ' . $tag->variable;
				} else {
					$function .= ', ' . implode( '|', $tag->types ) . ' ' . $tag->variable;
				}
			}


			$function .= ')';
		}

		return $function;
	}

	/**
	 * Generates single hook.
	 *
	 * @param HookDto $hook
	 * @return void
	 */
	protected function generateSingleHook( $hook ) {

		$content = $this->_markdownContent;

		// Title & Description
		$title       = $hook->name;
		$description = $hook->docBlock->description;
		$content     .= '### ' . $title . PHP_EOL;
		$content     .= $description . PHP_EOL;

		$function = $this->generateFunction( $hook );

		$content .= <<<EOT
```php
$function
```

EOT;

		if ( ! empty( $hook->docBlock->tags ) ) {
			$content .= '#### Arguments' . PHP_EOL;
			foreach ( $hook->docBlock->tags as $tag ) {
				if ( $tag->name === 'param' ) {
					$content .= sprintf(
						            '* `%s` (%s) %s',
						            $tag->variable,
						            '_' . implode( '_|_', $tag->types ) . '_',
						            $tag->content
					            ) . PHP_EOL;
				}

			}
		}

		$this->_markdownContent = $content;
	}

	/**
	 * Saves markdown content into specified location.
	 *
	 * @return bool
	 * @throws Exception
	 */
	protected function saveContent() {
		$filePath = $this->_saveLocation;

		if ( ! file_exists( $filePath ) ) {
			throw new \Exception( sprintf( 'Location %s does not exist', $this->_saveLocation ) );
		}

		return @file_put_contents( $filePath, $this->_markdownContent ) !== false;
	}
}


class HookTagDto {
	public $name;
	public $content;
	public $types = [];
	public $variable;
}

class HookDocBlockDto {
	public $description;
	public $longDescription;

	/**
	 * @var HookTagDto
	 */
	public $tags = [];
}

class HookDto {
	/**
	 * @var string
	 */
	public $name;

	/**
	 * @var string
	 */
	public $path;

	/**
	 * @var integer
	 */
	public $line;
	/**
	 * @var integer
	 */
	public $endLine;
	/**
	 * @var string
	 */
	public $type;

	/**
	 * @var array List of hook arguments.
	 */
	public $arguments = [];

	/**
	 * @var HookDocBlockDto
	 */
	public $docBlock;
}


/**
 * Class HooksParser helps to parse WordPress hooks in provided directory.
 *
 * Usage example:
 *
 * ```php
 * $parser = new HooksParser([
 *      'scanDirectory'     => 'path/to//wp-content/plugins/your-plugin',
 *      'ignoreDirectories' => [
 *          'vendor',
 *          'scripts',
 *          'languages',
 *          'assets'
 *      ],
 *      'scanExtensions'    => [ 'php' ],
 * ]);
 * $parsedItems = $parser->parse(); // Do something with found items from all files in directory
 * ```
 */
class HooksParser {
	/**
	 * @var string Directory to scan.
	 */
	public $scanDirectory;

	/**
	 * @var array List of extension to scan.
	 */
	public $scanExtensions = [ 'php' => 'php' ];

	/**
	 * @var array List of directories to ignore.
	 */
	public $ignoreDirectories = [];


	/**
	 * @var HookDto[] List of parsed hooks. Empty array when nothing was parsed.
	 */
	private $_foundHooks = [];

	/**
	 * HooksParser constructor.
	 *
	 * @param array $options List of configuration options.
	 */
	public function __construct( $options = [] ) {
		$this->normalizeAndPrepareOptions( $options );
	}

	/**
	 * Executes parsing.
	 *
	 * @return HookDto[]
	 */
	public function parse() {

		$filePaths = $this->getDirectoryFiles();

		$parsed = \WP_Parser\parse_files( $filePaths, $this->scanDirectory );

		foreach ( $parsed as $p ) {
			if ( isset( $p['hooks'] ) ) {

				var_dump( $p );

				foreach ( $p['hooks'] as $hook ) {
					$hookDto = new HookDto();

					$hookDto->name      = $hook['name'];
					$hookDto->path      = $p['path'];
					$hookDto->line      = $hook['line'];
					$hookDto->endLine   = $hook['end_line'];
					$hookDto->type      = $hook['type'];
					$hookDto->arguments = $hook['arguments'] ?? [];

					$hookDocBlock                  = new HookDocBlockDto();
					$hookDocBlock->description     = $hook['doc']['description'] ?? null;
					$hookDocBlock->longDescription = $hook['doc']['long_description'] ?? null;


					$tags = $hook['doc']['tags'] ?? [];

					foreach ( $tags as $tag ) {
						$tagDto               = new HookTagDto();
						$tagDto->name         = $tag['name'];
						$tagDto->content      = $tag['content'];
						$tagDto->types        = $tag['types'] ?? [];
						$tagDto->variable     = $tag['variable'] ?? null;
						$hookDocBlock->tags[] = $tagDto;
					}

					$hookDto->docBlock = $hookDocBlock;

					$this->_foundHooks[] = $hookDto;
				}
			}
		}

		return $this->_foundHooks;
	}

	/**
	 * Normalizes options.
	 *
	 * @param array $options
	 *
	 * @throws Exception
	 */
	protected function normalizeAndPrepareOptions( array $options ) {
		foreach ( $options as $optionName => $optionValue ) {
			switch ( $optionName ) {
				case 'scanExtensions':
					$this->setScanExtensions( $optionValue );
					break;
				case 'scanDirectory':
					if ( ! is_dir( $optionValue ) ) {
						throw new \Exception( sprintf( 'Directory %s does not exist', $optionValue ) );
					}
					$this->{$optionName} = $optionValue;
					break;
				default:
					$this->{$optionName} = $optionValue;
			}
		}
	}

	/**
	 * Normalises list of extensions.
	 *
	 * @param array $extensions List of extensions.
	 *
	 * @return array
	 */
	protected function setScanExtensions( $extensions ) {
		$scanExtension = [];
		foreach ( $extensions as $extension ) {
			$scanExtension[ $extension ] = trim( preg_replace( '/\W/m', '', $extension ) );
		}

		return $this->scanExtensions = $scanExtension;
	}

	/**
	 * Returns list of files in provided directory.
	 *
	 * @return array
	 */
	protected function getDirectoryFiles() {
		$recursiveIteratorObject = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $this->scanDirectory ) );

		$ignoreDirectories = $this->ignoreDirectories;


		$files = [];
		/**
		 * @var $fileOrFolder RecursiveDirectoryIterator
		 */
		foreach ( $recursiveIteratorObject as $fileOrFolder ) {

			if ( ! empty( $ignoreDirectories ) ) {

				$absolutePath = $fileOrFolder->getPathname();

				foreach ( $ignoreDirectories as $ignoreDirectoryName ) {

					if ( strpos( $absolutePath, $ignoreDirectoryName ) !== false ) {
						continue 2;
					}
				}

				if ( ! $fileOrFolder->isDir() && isset( $this->scanExtensions[ $fileOrFolder->getExtension() ] ) ) {


					$files[] = $fileOrFolder->getPathname();
				}
			}
		}

		return $files;
	}
}