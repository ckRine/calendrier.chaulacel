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

/* agenda.twig */
class __TwigTemplate_869de1508c4735c3f45b69c18391cba7 extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->blocks = [
            'title' => [$this, 'block_title'],
            'content' => [$this, 'block_content'],
            'javascripts' => [$this, 'block_javascripts'],
        ];
    }

    protected function doGetParent(array $context)
    {
        // line 1
        return "base.twig";
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        $this->parent = $this->loadTemplate("base.twig", "agenda.twig", 1);
        yield from $this->parent->unwrap()->yield($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_title($context, array $blocks = [])
    {
        $macros = $this->macros;
        yield "Agenda - ChronoGestCal";
        return; yield '';
    }

    // line 5
    public function block_content($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 6
        yield "    ";
        yield from         $this->loadTemplate("header/header.twig", "agenda.twig", 6)->unwrap()->yield($context);
        // line 7
        yield "    
    <div class=\"agenda-container\">
        <h1>Agenda hebdomadaire</h1>
        
        <div class=\"agenda-view\">
            <div class=\"agenda-sidebar\">
                <div class=\"agenda-date-picker\">
                    <h3>Sélectionner une date</h3>
                    <input type=\"date\" id=\"agenda-date\" value=\"";
        // line 15
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Twig\Extension\CoreExtension']->formatDate(($context["now"] ?? null), "Y-m-d"), "html", null, true);
        yield "\">
                    <button id=\"go-to-date\">Afficher</button>
                </div>
                

            </div>
            
            <div class=\"agenda-content\">
                <div class=\"agenda-header\">
                    <button id=\"prev-week\">&lt; Semaine précédente</button>
                    <h2 id=\"current-week-range\">Semaine du ";
        // line 25
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Twig\Extension\CoreExtension']->formatDate(($context["now"] ?? null), "d/m/Y"), "html", null, true);
        yield "</h2>
                    <button id=\"next-week\">Semaine suivante &gt;</button>
                </div>
                
                <div class=\"agenda-grid\" id=\"agenda-grid\">
                    <!-- Le contenu sera généré dynamiquement par JavaScript -->
                    <div class=\"loading\">Chargement de l'agenda...</div>
                </div>
            </div>
        </div>
    </div>
    
    ";
        // line 37
        yield from         $this->loadTemplate("day-popup.twig", "agenda.twig", 37)->unwrap()->yield($context);
        // line 38
        yield "    ";
        yield from         $this->loadTemplate("auth/auth-form.twig", "agenda.twig", 38)->unwrap()->yield($context);
        return; yield '';
    }

    // line 41
    public function block_javascripts($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 42
        yield "    ";
        yield from $this->yieldParentBlock("javascripts", $context, $blocks);
        yield "
    <script src=\"";
        // line 43
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(($context["STATICS_PATH"] ?? null), "html", null, true);
        yield "/js/agenda.js\"></script>
";
        return; yield '';
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "agenda.twig";
    }

    /**
     * @codeCoverageIgnore
     */
    public function isTraitable()
    {
        return false;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDebugInfo()
    {
        return array (  119 => 43,  114 => 42,  110 => 41,  104 => 38,  102 => 37,  87 => 25,  74 => 15,  64 => 7,  61 => 6,  57 => 5,  49 => 3,  38 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("{% extends \"base.twig\" %}

{% block title %}Agenda - ChronoGestCal{% endblock %}

{% block content %}
    {% include 'header/header.twig' %}
    
    <div class=\"agenda-container\">
        <h1>Agenda hebdomadaire</h1>
        
        <div class=\"agenda-view\">
            <div class=\"agenda-sidebar\">
                <div class=\"agenda-date-picker\">
                    <h3>Sélectionner une date</h3>
                    <input type=\"date\" id=\"agenda-date\" value=\"{{ now|date('Y-m-d') }}\">
                    <button id=\"go-to-date\">Afficher</button>
                </div>
                

            </div>
            
            <div class=\"agenda-content\">
                <div class=\"agenda-header\">
                    <button id=\"prev-week\">&lt; Semaine précédente</button>
                    <h2 id=\"current-week-range\">Semaine du {{ now|date('d/m/Y') }}</h2>
                    <button id=\"next-week\">Semaine suivante &gt;</button>
                </div>
                
                <div class=\"agenda-grid\" id=\"agenda-grid\">
                    <!-- Le contenu sera généré dynamiquement par JavaScript -->
                    <div class=\"loading\">Chargement de l'agenda...</div>
                </div>
            </div>
        </div>
    </div>
    
    {% include 'day-popup.twig' %}
    {% include 'auth/auth-form.twig' %}
{% endblock %}

{% block javascripts %}
    {{ parent() }}
    <script src=\"{{ STATICS_PATH }}/js/agenda.js\"></script>
{% endblock %}", "agenda.twig", "C:\\Users\\ck_ri\\Developpement\\chaulacel\\calendrier.chaulacel\\templates\\agenda.twig");
    }
}
