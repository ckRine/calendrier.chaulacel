<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\CoreExtension;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;

/* day-popup.twig */
class __TwigTemplate_61b546e9285b83a6c8004b88e7354b4a extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        yield "<div id=\"day-popup\" class=\"day-popup\">
\t<div class=\"day-popup-content\">
\t\t\t<span class=\"close\" onclick=\"hideDayPopup()\">&times;</span>
\t\t\t<h2 id=\"day-popup-title\"></h2>
\t\t\t<div class=\"day-popup-columns\">
\t\t\t\t\t<div class=\"day-column day-info-column\">
\t\t\t\t\t\t\t<h3>Informations</h3>
\t\t\t\t\t\t\t<div id=\"day-popup-info\"></div>
\t\t\t\t\t</div>
\t\t\t\t\t<div class=\"day-column day-events-column\">
\t\t\t\t\t\t\t<h3>Événements</h3>
\t\t\t\t\t\t\t<div id=\"day-popup-events\"></div>
\t\t\t\t\t</div>
\t\t\t\t\t<div class=\"day-column day-zones-column\">
\t\t\t\t\t\t\t<h3>Zones</h3>
\t\t\t\t\t\t\t<div id=\"day-popup-zones\"></div>
\t\t\t\t\t</div>
\t\t\t</div>
\t</div>
</div>";
        return; yield '';
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "day-popup.twig";
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDebugInfo()
    {
        return array ();
    }

    public function getSourceContext()
    {
        return new Source("<div id=\"day-popup\" class=\"day-popup\">
\t<div class=\"day-popup-content\">
\t\t\t<span class=\"close\" onclick=\"hideDayPopup()\">&times;</span>
\t\t\t<h2 id=\"day-popup-title\"></h2>
\t\t\t<div class=\"day-popup-columns\">
\t\t\t\t\t<div class=\"day-column day-info-column\">
\t\t\t\t\t\t\t<h3>Informations</h3>
\t\t\t\t\t\t\t<div id=\"day-popup-info\"></div>
\t\t\t\t\t</div>
\t\t\t\t\t<div class=\"day-column day-events-column\">
\t\t\t\t\t\t\t<h3>Événements</h3>
\t\t\t\t\t\t\t<div id=\"day-popup-events\"></div>
\t\t\t\t\t</div>
\t\t\t\t\t<div class=\"day-column day-zones-column\">
\t\t\t\t\t\t\t<h3>Zones</h3>
\t\t\t\t\t\t\t<div id=\"day-popup-zones\"></div>
\t\t\t\t\t</div>
\t\t\t</div>
\t</div>
</div>", "day-popup.twig", "C:\\Users\\ck_ri\\Developpement\\chaulacel\\calendrier.chaulacel\\templates\\day-popup.twig");
    }
}
