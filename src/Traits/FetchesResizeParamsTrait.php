<?php

namespace Signapps\Webplugin\Api\Traits;

use League\Fractal\ParamBag;

trait FetchesResizeParamsTrait
{

    /**
     * Returns resize params from the request URL.
     *
     * @param ParamBag $params
     * @return array
     */
    protected function getResizeParams(ParamBag $params = null)
    {
        if (null === $params || true === empty($params->get('resize'))) {
            return [];
        }

        return $this->parseParams($params->get('resize'));
    }

    protected function parseParams($params, $separator = '=')
    {
        $parsed = [];

        foreach ($params as $optionSet) {
            $options = explode($separator, $optionSet);
            $parsed[$options[0]] = $options[1];
        }

        return $parsed;
    }
}
