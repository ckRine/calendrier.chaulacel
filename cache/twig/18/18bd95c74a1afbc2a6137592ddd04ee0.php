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

/* header/header.twig */
class __TwigTemplate_8fdac17354f038db54611dfbdeaf4893 extends Template
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
        yield "<header>
    <div class=\"logo\">
        <a href=\"index.php\">ChronoGestCal</a>
    </div>
    <nav>
        <a href=\"index.php\" class=\"";
        // line 6
        yield (((($context["current_page"] ?? null) == "calendrier")) ? ("active") : (""));
        yield "\">Calendrier</a>
        <a href=\"agenda.php\" class=\"";
        // line 7
        yield (((($context["current_page"] ?? null) == "agenda")) ? ("active") : (""));
        yield "\">Agenda</a>
        ";
        // line 8
        if (($context["is_logged_in"] ?? null)) {
            // line 9
            yield "            <a href=\"mon-compte.php\" class=\"";
            yield (((($context["current_page"] ?? null) == "mon-compte")) ? ("active") : (""));
            yield "\">Mon compte</a>
        ";
        }
        // line 11
        yield "    </nav>
    <div class=\"auth-controls\">
        ";
        // line 13
        if (($context["is_logged_in"] ?? null)) {
            // line 14
            yield "            <div class=\"user-menu\">
                <div class=\"avatar-container\" onclick=\"toggleUserMenu()\">
                    <div class=\"avatar\">";
            // line 16
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(Twig\Extension\CoreExtension::upper($this->env->getCharset(), Twig\Extension\CoreExtension::slice($this->env->getCharset(), CoreExtension::getAttribute($this->env, $this->source, ($context["user"] ?? null), "email", [], "any", false, false, false, 16), 0, 1)), "html", null, true);
            yield "</div>
                </div>
                <div class=\"user-dropdown\" id=\"user-dropdown\">
                    <div class=\"user-info\">";
            // line 19
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["user"] ?? null), "email", [], "any", false, false, false, 19), "html", null, true);
            yield "</div>
                    <div class=\"dropdown-divider\"></div>
                    <button onclick=\"window.location.href='mon-compte.php'\">Mon compte</button>
                    <button onclick=\"logout()\">Déconnexion</button>
                </div>
            </div>
        ";
        } else {
            // line 26
            yield "            <button onclick=\"showAuthForm('login')\">Connexion</button>
            <button onclick=\"showAuthForm('register')\">Inscription</button>
        ";
        }
        // line 29
        yield "    </div>
</header>";
        return; yield '';
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "header/header.twig";
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
        return array (  92 => 29,  87 => 26,  77 => 19,  71 => 16,  67 => 14,  65 => 13,  61 => 11,  55 => 9,  53 => 8,  49 => 7,  45 => 6,  38 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("<header>
    <div class=\"logo\">
        <a href=\"index.php\">ChronoGestCal</a>
    </div>
    <nav>
        <a href=\"index.php\" class=\"{{ current_page == 'calendrier' ? 'active' : '' }}\">Calendrier</a>
        <a href=\"agenda.php\" class=\"{{ current_page == 'agenda' ? 'active' : '' }}\">Agenda</a>
        {% if is_logged_in %}
            <a href=\"mon-compte.php\" class=\"{{ current_page == 'mon-compte' ? 'active' : '' }}\">Mon compte</a>
        {% endif %}
    </nav>
    <div class=\"auth-controls\">
        {% if is_logged_in %}
            <div class=\"user-menu\">
                <div class=\"avatar-container\" onclick=\"toggleUserMenu()\">
                    <div class=\"avatar\">{{ user.email|slice(0, 1)|upper }}</div>
                </div>
                <div class=\"user-dropdown\" id=\"user-dropdown\">
                    <div class=\"user-info\">{{ user.email }}</div>
                    <div class=\"dropdown-divider\"></div>
                    <button onclick=\"window.location.href='mon-compte.php'\">Mon compte</button>
                    <button onclick=\"logout()\">Déconnexion</button>
                </div>
            </div>
        {% else %}
            <button onclick=\"showAuthForm('login')\">Connexion</button>
            <button onclick=\"showAuthForm('register')\">Inscription</button>
        {% endif %}
    </div>
</header>", "header/header.twig", "C:\\Users\\ck_ri\\Developpement\\chaulacel\\calendrier.chaulacel\\templates\\header\\header.twig");
    }
}
