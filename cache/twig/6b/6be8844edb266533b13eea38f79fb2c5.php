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

/* calendrier.twig */
class __TwigTemplate_fca3e39a262996e57d1006f12096598a extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->blocks = [
            'content' => [$this, 'block_content'],
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
        $this->parent = $this->loadTemplate("base.twig", "calendrier.twig", 1);
        yield from $this->parent->unwrap()->yield($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_content($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 4
        yield "    ";
        yield from         $this->loadTemplate("header/header.twig", "calendrier.twig", 4)->unwrap()->yield($context);
        // line 5
        yield "    ";
        yield from         $this->loadTemplate("auth/auth-form.twig", "calendrier.twig", 5)->unwrap()->yield($context);
        // line 6
        yield "    ";
        yield from         $this->loadTemplate("controls/controls.twig", "calendrier.twig", 6)->unwrap()->yield($context);
        // line 7
        yield "    ";
        yield from         $this->loadTemplate("day-popup.twig", "calendrier.twig", 7)->unwrap()->yield($context);
        // line 8
        yield "    
    <div class=\"calendar-container\">
        <div class=\"nav\">
            <button onclick=\"prevMonths()\">◄ Reculer</button>
            <select id=\"year\" onchange=\"updateYear(this.value)\">
                ";
        // line 13
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable(range(($this->extensions['Twig\Extension\CoreExtension']->formatDate(($context["now"] ?? null), "Y") - 5), ($this->extensions['Twig\Extension\CoreExtension']->formatDate(($context["now"] ?? null), "Y") + 5)));
        foreach ($context['_seq'] as $context["_key"] => $context["i"]) {
            // line 14
            yield "                    <option value=\"";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($context["i"], "html", null, true);
            yield "\" ";
            if (($context["i"] == $this->extensions['Twig\Extension\CoreExtension']->formatDate(($context["now"] ?? null), "Y"))) {
                yield "selected";
            }
            yield ">";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($context["i"], "html", null, true);
            yield "</option>
                ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['i'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 16
        yield "            </select>
            <button onclick=\"nextMonths()\">Avancer ►</button>
            <button onclick=\"goToToday()\">Aujourd'hui</button>
            <div class=\"goto-date\">
                <input type=\"date\" id=\"date-picker\" aria-label=\"Aller à une date\">
                <button onclick=\"goToDate()\">Aller à</button>
            </div>
        </div>
        <div class=\"calendar-nav prev\" onclick=\"prevMonths()\">&lt;</div>
        <div class=\"calendar\" id=\"calendar\"></div>
        <div class=\"calendar-nav next\" onclick=\"nextMonths()\">&gt;</div>
    </div>
";
        return; yield '';
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "calendrier.twig";
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
        return array (  89 => 16,  74 => 14,  70 => 13,  63 => 8,  60 => 7,  57 => 6,  54 => 5,  51 => 4,  47 => 3,  36 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("{% extends \"base.twig\" %}

{% block content %}
    {% include 'header/header.twig' %}
    {% include 'auth/auth-form.twig' %}
    {% include 'controls/controls.twig' %}
    {% include 'day-popup.twig' %}
    
    <div class=\"calendar-container\">
        <div class=\"nav\">
            <button onclick=\"prevMonths()\">◄ Reculer</button>
            <select id=\"year\" onchange=\"updateYear(this.value)\">
                {% for i in range((now|date('Y'))-5, (now|date('Y'))+5) %}
                    <option value=\"{{ i }}\" {% if i == now|date('Y') %}selected{% endif %}>{{ i }}</option>
                {% endfor %}
            </select>
            <button onclick=\"nextMonths()\">Avancer ►</button>
            <button onclick=\"goToToday()\">Aujourd'hui</button>
            <div class=\"goto-date\">
                <input type=\"date\" id=\"date-picker\" aria-label=\"Aller à une date\">
                <button onclick=\"goToDate()\">Aller à</button>
            </div>
        </div>
        <div class=\"calendar-nav prev\" onclick=\"prevMonths()\">&lt;</div>
        <div class=\"calendar\" id=\"calendar\"></div>
        <div class=\"calendar-nav next\" onclick=\"nextMonths()\">&gt;</div>
    </div>
{% endblock %}", "calendrier.twig", "C:\\Users\\ck_ri\\Developpement\\chaulacel\\calendrier.chaulacel\\templates\\calendrier.twig");
    }
}
