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

/* base.twig */
class __TwigTemplate_7327562f6e220f08e86d55e55bc13e41 extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
            'title' => [$this, 'block_title'],
            'stylesheets' => [$this, 'block_stylesheets'],
            'content' => [$this, 'block_content'],
            'javascripts' => [$this, 'block_javascripts'],
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 1
        yield "<!DOCTYPE html>
<html lang=\"fr\">
    <head>
        <meta charset=\"UTF-8\">
        <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
        <meta http-equiv=\"Content-Security-Policy\" content=\"
            default-src 'self';
            script-src 'self' 'unsafe-inline' https://apis.google.com;
            connect-src 'self' https://data.education.gouv.fr https://calendrier.api.gouv.fr https://www.googleapis.com;
            style-src 'self' 'unsafe-inline';
            img-src 'self' data:;
            object-src 'none';
        \">
        <title>";
        // line 14
        yield from $this->unwrap()->yieldBlock('title', $context, $blocks);
        yield "</title>
        
        ";
        // line 16
        yield from $this->unwrap()->yieldBlock('stylesheets', $context, $blocks);
        // line 19
        yield "        
        <script>
            const STATICS_PATH = '";
        // line 21
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(($context["STATICS_PATH"] ?? null), "html", null, true);
        yield "';
            const MODULES_PATH = '";
        // line 22
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(($context["MODULES_PATH"] ?? null), "html", null, true);
        yield "';
        </script>
    </head>
    <body>
        <div class=\"container\">
            ";
        // line 27
        yield from $this->unwrap()->yieldBlock('content', $context, $blocks);
        // line 28
        yield "        </div>
        
        ";
        // line 30
        yield from $this->unwrap()->yieldBlock('javascripts', $context, $blocks);
        // line 38
        yield "    </body>
</html>";
        return; yield '';
    }

    // line 14
    public function block_title($context, array $blocks = [])
    {
        $macros = $this->macros;
        yield "ChronoGestCal";
        return; yield '';
    }

    // line 16
    public function block_stylesheets($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 17
        yield "        <link rel=\"stylesheet\" href=\"";
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(($context["STATICS_PATH"] ?? null), "html", null, true);
        yield "/css/main.css\">
        ";
        return; yield '';
    }

    // line 27
    public function block_content($context, array $blocks = [])
    {
        $macros = $this->macros;
        return; yield '';
    }

    // line 30
    public function block_javascripts($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 31
        yield "        <script src=\"";
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(($context["STATICS_PATH"] ?? null), "html", null, true);
        yield "/js/script.js\"></script>
        <script src=\"";
        // line 32
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(($context["STATICS_PATH"] ?? null), "html", null, true);
        yield "/js/google-calendar.js\"></script>
        <script src=\"";
        // line 33
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(($context["STATICS_PATH"] ?? null), "html", null, true);
        yield "/js/auth.js\"></script>
        <script src=\"";
        // line 34
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(($context["STATICS_PATH"] ?? null), "html", null, true);
        yield "/js/day-popup.js\"></script>
        <script src=\"";
        // line 35
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(($context["STATICS_PATH"] ?? null), "html", null, true);
        yield "/js/print.js\"></script>
        <script src=\"";
        // line 36
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(($context["STATICS_PATH"] ?? null), "html", null, true);
        yield "/js/export.js\"></script>
        ";
        return; yield '';
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "base.twig";
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
        return array (  146 => 36,  142 => 35,  138 => 34,  134 => 33,  130 => 32,  125 => 31,  121 => 30,  114 => 27,  106 => 17,  102 => 16,  94 => 14,  88 => 38,  86 => 30,  82 => 28,  80 => 27,  72 => 22,  68 => 21,  64 => 19,  62 => 16,  57 => 14,  42 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("<!DOCTYPE html>
<html lang=\"fr\">
    <head>
        <meta charset=\"UTF-8\">
        <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
        <meta http-equiv=\"Content-Security-Policy\" content=\"
            default-src 'self';
            script-src 'self' 'unsafe-inline' https://apis.google.com;
            connect-src 'self' https://data.education.gouv.fr https://calendrier.api.gouv.fr https://www.googleapis.com;
            style-src 'self' 'unsafe-inline';
            img-src 'self' data:;
            object-src 'none';
        \">
        <title>{% block title %}ChronoGestCal{% endblock %}</title>
        
        {% block stylesheets %}
        <link rel=\"stylesheet\" href=\"{{ STATICS_PATH }}/css/main.css\">
        {% endblock %}
        
        <script>
            const STATICS_PATH = '{{ STATICS_PATH }}';
            const MODULES_PATH = '{{ MODULES_PATH }}';
        </script>
    </head>
    <body>
        <div class=\"container\">
            {% block content %}{% endblock %}
        </div>
        
        {% block javascripts %}
        <script src=\"{{ STATICS_PATH }}/js/script.js\"></script>
        <script src=\"{{ STATICS_PATH }}/js/google-calendar.js\"></script>
        <script src=\"{{ STATICS_PATH }}/js/auth.js\"></script>
        <script src=\"{{ STATICS_PATH }}/js/day-popup.js\"></script>
        <script src=\"{{ STATICS_PATH }}/js/print.js\"></script>
        <script src=\"{{ STATICS_PATH }}/js/export.js\"></script>
        {% endblock %}
    </body>
</html>", "base.twig", "C:\\Users\\ck_ri\\Developpement\\chaulacel\\calendrier.chaulacel\\templates\\base.twig");
    }
}
