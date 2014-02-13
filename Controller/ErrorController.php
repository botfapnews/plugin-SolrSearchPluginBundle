<?php
/**
 * @package   Newscoop\SolrSearchPluginBundle
 * @author    Mischa Gorinskat <mischa.gorinskat@sourcefabric.org>
 * @copyright 2014 Sourcefabric o.p.s.
 * @license   http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\SolrSearchPluginBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ErrorController extends Controller
{
    /**
     * @Route("/search/error", name="search_error")
     * @Route("/{language}/search/error", name="search_error_lang")
     */
    public function searchAction(Request $request, $language = null)
    {
        $templatesService = $this->container->get('newscoop.templates.service');

        return new Response(
            $templatesService->fetchTemplate("_views/search_error.tpl"),
            503
        );
    }

    /**
     * @Route("/omniticker/error", name="omniticker_error")
     * @Route("/{language}/omniticker/error", name="omniticker_error_lang")
     */
    public function omnitickerAction(Request $request, $language = null)
    {
        $templatesService = $this->container->get('newscoop.templates.service');

        return new Response(
            $templatesService->fetchTemplate("_views/topic_notfound.tpl"),
            404
        );
    }

    /**
     * @Route("/themen/error", name="topic_error")
     * @Route("/{language}/themen/error", name="topic_error_lang")
     */
    public function topicAction(Request $request, $language = null)
    {
        $templatesService = $this->container->get('newscoop.templates.service');

        return new Response(
            $templatesService->fetchTemplate("_views/topic_notfound.tpl"),
            404
        );
    }
}
