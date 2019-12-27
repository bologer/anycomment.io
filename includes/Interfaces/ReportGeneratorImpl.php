<?php
/**
 * User: alex
 * Date: 12/26/19
 * Time: 7:48 PM
 * Author: Alexander Teshabaev <sasha.tesh@gmail.com>
 */

namespace AnyComment\Interfaces;


interface ReportGeneratorImpl
{
    /**
     * Generates report.
     *
     * @return string
     */
    public function generate();
}