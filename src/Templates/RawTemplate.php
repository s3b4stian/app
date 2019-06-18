<?php

/**
 * Linna App.
 *
 * @author Sebastian Rapetti <sebastian.rapetti@alice.it>
 * @copyright (c) 2018, Sebastian Rapetti
 * @license http://opensource.org/licenses/MIT MIT License
 */
declare(strict_types=1);

namespace App\Templates;

use Linna\Mvc\TemplateInterface;

/**
 * Raw Template.
 * Print raw strings to output buffer.
 */
class RawTemplate implements TemplateInterface
{
    /**
     * @var array Data for view
     */
    public $data = [];

    /**
     * Class Constructor.
     */
    /*public function __construct()
    {
        $this->data = (object) null;
    }*/

    /**
     * Set the output data.
     *
     * @param array $data
     */
    public function setData(array $data): void
    {
        $this->data = $data;
    }

    /**
     * Return void string.
     */
    public function getOutput(): string
    {
        \ob_start();

        //require "{$this->templateDir}/{$this->template}.html";
        echo \implode('', $this->data);

        return \ob_get_clean();
        //return implode('', $this->data);
    }
}