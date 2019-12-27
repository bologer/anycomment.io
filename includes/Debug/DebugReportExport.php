<?php

namespace AnyComment\Debug;

use AnyComment\AnyCommentCore;
use AnyComment\Interfaces\ReportGeneratorImpl;
use ZipArchive;

/**
 * Class DebugReportExport helps to export debug information via zipping report & log file into archive.
 *
 * It has ability to just generate ZIP archive or generate archive & stream it directly to user.
 *
 * @author Alexander Teshabaev <sasha.tesh@gmail.com>
 * @package AnyComment\Debug
 */
class DebugReportExport
{
    /**
     * @var ReportGeneratorImpl
     */
    private $_report;

    /**
     * @var string Archive name.
     */
    private $_archive_name;

    /**
     * DebugReportExport constructor.
     *
     * @param ReportGeneratorImpl $report
     */
    public function __construct(ReportGeneratorImpl $report)
    {
        $this->_report = $report;
    }

    /**
     * Exports archive without streaming.
     *
     * @return bool
     */
    public function export()
    {
        return $this->buildArchive() !== false;
    }

    /**
     * Streams generated archive.
     */
    public function streamExport()
    {
        $archive_path = $this->buildArchive();

        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: public");
        header("Content-Description: File Transfer");
        header("Content-type: application/octet-stream");
        header("Content-Disposition: attachment; filename=\"" . $this->get_archive_name() . "\"");
        header("Content-Transfer-Encoding: binary");
        header("Content-Length: " . filesize($archive_path));
        ob_clean();
        flush();
        @readfile($archive_path);
        @unlink($archive_path);
        exit;
    }

    /**
     * Builds archive & puts require debug information inside.
     *
     * @return bool|string Path to archive on success or false on failure.
     */
    protected function buildArchive()
    {
        $archive_path_with_name = $this->get_save_path() . DIRECTORY_SEPARATOR . $this->get_archive_name();

        $zip = new ZipArchive;
        if ($zip->open($archive_path_with_name, ZipArchive::CREATE) === true) {
            // Add a file new.txt file to zip using the text specified
            $zip->addFromString('report.txt', $this->_report->generate());

            $handlers = AnyCommentCore::logger()->getHandlers();

            if (isset($handlers[0]) && ! empty($handlers[0]->getUrl())) {
                $zip->addFromString('debug.log', @file_get_contents($handlers[0]->getUrl()));
            }

            // All files are added, so close the zip file.
            $zip->close();

            return $archive_path_with_name;
        }


        return false;
    }

    /**
     * Returns absolute save path.
     *
     * @return string|null
     */
    public function get_save_path()
    {
        $upload_dirs_meta = wp_get_upload_dir();

        if (isset($upload_dirs_meta['path'])) {
            return $upload_dirs_meta['path'];
        }

        return null;
    }

    /**
     * Generates unique archive name.
     *
     * @return string
     */
    public function get_archive_name()
    {
        if ($this->_archive_name === null) {
            $unique_hash = uniqid();

            $this->_archive_name = sprintf('anycomment-debug_%s.zip', $unique_hash);
        }

        return $this->_archive_name;
    }
}
