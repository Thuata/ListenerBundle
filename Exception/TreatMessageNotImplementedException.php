<?php
/**
 * Created by Enjoy Your Business.
 * Date: 08/11/2016
 * Time: 16:35
 * Copyright 2014 Enjoy Your Business - RCS Bourges B 800 159 295 ©
 */

namespace Thuata\ListenerBundle\Exception;


/**
 * Class TreatMessageNotImplementedException
 *
 * @package   Thuata\ListenerBundle\Exception
 *
 * @author    Emmanuel Derrien <emmanuel.derrien@enjoyyourbusiness.fr>
 * @author    Anthony Maudry <anthony.maudry@enjoyyourbusiness.fr>
 * @author    Loic Broc <loic.broc@enjoyyourbusiness.fr>
 * @author    Rémy Mantéi <remy.mantei@enjoyyourbusiness.fr>
 * @author    Lucien Bruneau <lucien.bruneau@enjoyyourbusiness.fr>
 * @author    Matthieu Prieur <matthieu.prieur@enjoyyourbusiness.fr>
 * @copyright 2014 Enjoy Your Business - RCS Bourges B 800 159 295 ©
 */
class TreatMessageNotImplementedException extends \Exception
{
    const MESSAGE_FORMAT = '%s::treatMessage method must be implemented';
    const ERROR_CODE = 500;

    /**
     * TreatMessageNotImplementedException constructor.
     *
     * @param string $class
     */
    public function __construct($class)
    {
        parent::__construct(sprintf(self::MESSAGE_FORMAT, $class), self::ERROR_CODE);
    }
}