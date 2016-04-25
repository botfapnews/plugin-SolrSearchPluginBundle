<?php
/**
 * @author    Mischa Gorinskat <mischa.gorinskat@sourcefabric.org>
 * @copyright 2015 Sourcefabric o.p.s.
 * @license   http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * Builds the Solr FQ query.
 *
 * Type:     function
 * Name:     build_solr_fq
 * Purpose:
 *
 * @param array $p_params
 *
 * @return string $solrFq
 *                The Solr FQ requested
 *
 * @example
 *  {{ list_search_results_solr fq="{{ build_solr_fq }}" }}
 *  {{ list_search_results_solr fq="{{ build_solr_fq type=$smarty.post.type }}" }}
 */
function smarty_function_build_solr_fq($p_params = array(), &$p_smarty)
{
    $solrFq = '';

    // The $p_params override the $_GET
    $acceptedParams = array('type', 'published', 'from', 'to', 'dateformat', 'section_number');
    $cleanParam = array();

    foreach ($acceptedParams as $key) {
        if (array_key_exists($key, $p_params) && !empty($p_params[$key])) {
            $cleanParam[$key] = $p_params[$key];
        } elseif (array_key_exists($key, $_GET) && !empty($_GET[$key])) {
            $cleanParam[$key] = $_GET[$key];
        }
    }

    if (array_key_exists('published', $cleanParam) && !empty($cleanParam['published'])) {
        $published = '';

        switch ($cleanParam['published']) {
            case '24h':
                $published = '[NOW-1DAY/HOUR TO *]';
                break;
            case '7d':
                $published = '[NOW-7DAYS/DAY TO *]';
                break;
            case '14d':
                $published = '[NOW-14DAYS/DAY TO *]';
                break;
            case '1m':
                $published = '[NOW-1MONTH/DAY TO *]';
                break;
            case '1y':
                $published = '[NOW-1YEAR/DAY TO *]';
                break;
            case '*':
                $published = '';
                break;
            default:
                if ($cleanParam['published'] == '') {
                    $published = '';
                } else {
                    $published = $cleanParam['published'];
                }
                break;
        }
    }

    if (array_key_exists('type', $cleanParam) && !empty($cleanParam['type'])) {
        if (!is_array($cleanParam['type'])) {
            $cleanParam['type'] = array(trim($cleanParam['type'], '()'));
        }
        $solrFq .= sprintf('type:(%s)', implode(' OR ', $cleanParam['type']));
    }

    if (array_key_exists('from', $cleanParam) && !empty($cleanParam['from'])) {
        $fromDate = date_create_from_format($cleanParam['dateformat'], $cleanParam['from']);
        if ($fromDate instanceof \DateTime) {
            $solrFromDate = date_format($fromDate, 'Y-m-d').'T00:00:00Z/DAY';
        }
    }

    if (array_key_exists('to', $cleanParam) && !empty($cleanParam['to'])) {
        $toDate = date_create_from_format($cleanParam['dateformat'], $cleanParam['to']);
        if ($toDate instanceof \DateTime) {
            $solrToDate = date_format($toDate, 'Y-m-d').'T00:00:00Z/DAY';
        }
    }

    if (isset($solrFromDate) && isset($solrToDate) && $solrFromDate == $solrToDate) {
        $solrToDate = date_format($toDate, 'Y-m-d').'T23:59:99.999Z/DAY';
    }

    if (!empty($solrFromDate) && !empty($solrToDate)) {
        $published = '['.$solrFromDate.' TO '.$solrToDate.']';
    } elseif (!empty($solrFromDate)) {
        $published = '['.$solrFromDate.' TO *]';
    } elseif (!empty($solrToDate)) {
        $published = '[* TO '.$solrToDate.']';
    }

    if (!empty($published)) {
        if (!empty($solrFq)) {
            $solrFq .= ' AND ';
        }

        $solrFq .= 'published:'.$published;
    }

    if (array_key_exists('section_number', $cleanParam) && !empty($cleanParam['section_number'])) {
        if (!is_array($cleanParam['section_number'])) {
            $cleanParam['section_number'] = array(trim($cleanParam['section_number'], '()'));
        }

        if (!empty($solrFq)) {
            $solrFq .= ' AND ';
        }

        $solrFq .= sprintf('section_number:(%s)', implode(' OR ', $cleanParam['section_number']));
    }

    return $solrFq;
}
