<?php


include __DIR__ . '/../../phpdoc-parser/vendor/autoload.php';
//include __DIR__ . '/../../phpdoc-parser/lib/runner.php';


$hooksParser = new HooksParser( [
	'scanDirectory'     => '/Users/alex/PhpstormProjects/anycomment-wp/wp-content/plugins/anycomment',
	'ignoreDirectories' => [
		'vendor',
		'languages'
	]
] );

$parsedItems = $hooksParser->parse();

var_dump( $parsedItems );

$markdownContent = '';

foreach ( $parsedItems as $item ) {
	$title       = $item->name;
	$description = $item->docBlock->description;

	$markdownContent .= <<<EOT
	
### $title

$description

EOT;

	$arguments = '';

	if ( ! empty( $item->docBlock->tags ) ) {
		$markdownContent .= '#### Arguments' . PHP_EOL;
		foreach ( $item->docBlock->tags as $tag ) {
			if ( $tag->name === 'param' ) {
				$markdownContent .= sprintf(
					                    '* `%s` (%s) %s',
					                    $tag->variable,
					                    '_' . implode( '_|_', $tag->types ) . '_',
					                    $tag->content
				                    ) . PHP_EOL;
			}

		}
	}

	var_dump( $item->docBlock->tags );

//	$markdownContent .= implode(', ', $item->docBlock->tags);
}

$filePath = dirname( __FILE__ ) . '/../docs/hooks.md';

file_put_contents( $filePath, $markdownContent );


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
	 * @var ParsedItem[] List of parsed hooks. Empty array when nothing was parsed.
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

		$hooks = [];

		foreach ( $parsed as $p ) {
			if ( isset( $p['hooks'] ) ) {
				foreach ( $p['hooks'] as $hook ) {
					$hookDto = new HookDto();

					$hookDto->name      = $hook['name'];
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
						$tagDto->types        = $tag['types'];
						$tagDto->variable     = $tag['variable'];
						$hookDocBlock->tags[] = $tagDto;
					}

					$hookDto->docBlock = $hookDocBlock;

					$hooks[] = $hookDto;
				}
			}
		}

		return $hooks;
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