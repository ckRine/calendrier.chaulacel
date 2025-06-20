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

/* controls/controls.twig */
class __TwigTemplate_95275d3218c52ff6438450634be84f2f extends Template
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
        // line 1
        yield "<div class=\"controls\">
    <span>Vacances scolaires :</span>
    <label class=\"color-box zone-a\">
        <input type=\"checkbox\" value=\"A\" onchange=\"updateZones('A', this.checked)\" ";
        // line 4
        if (CoreExtension::inFilter("A", ($context["selectedZones"] ?? null))) {
            yield "checked";
        }
        yield ">
        Zone A
    </label>
    <label class=\"color-box zone-b\">
        <input type=\"checkbox\" value=\"B\" onchange=\"updateZones('B', this.checked)\" ";
        // line 8
        if (CoreExtension::inFilter("B", ($context["selectedZones"] ?? null))) {
            yield "checked";
        }
        yield ">
        Zone B
    </label>
    <label class=\"color-box zone-c\">
        <input type=\"checkbox\" value=\"C\" onchange=\"updateZones('C', this.checked)\" ";
        // line 12
        if (CoreExtension::inFilter("C", ($context["selectedZones"] ?? null))) {
            yield "checked";
        }
        yield ">
        Zone C
    </label>
</div>";
        return; yield '';
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "controls/controls.twig";
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
        return array (  61 => 12,  52 => 8,  43 => 4,  38 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("<div class=\"controls\">
    <span>Vacances scolaires :</span>
    <label class=\"color-box zone-a\">
        <input type=\"checkbox\" value=\"A\" onchange=\"updateZones('A', this.checked)\" {% if 'A' in selectedZones %}checked{% endif %}>
        Zone A
    </label>
    <label class=\"color-box zone-b\">
        <input type=\"checkbox\" value=\"B\" onchange=\"updateZones('B', this.checked)\" {% if 'B' in selectedZones %}checked{% endif %}>
        Zone B
    </label>
    <label class=\"color-box zone-c\">
        <input type=\"checkbox\" value=\"C\" onchange=\"updateZones('C', this.checked)\" {% if 'C' in selectedZones %}checked{% endif %}>
        Zone C
    </label>
</div>", "controls/controls.twig", "C:\\Users\\ck_ri\\Developpement\\chaulacel\\calendrier.chaulacel\\templates\\controls\\controls.twig");
    }
}
