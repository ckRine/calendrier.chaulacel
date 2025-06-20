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

/* auth/auth-form.twig */
class __TwigTemplate_fe6505d3d5c48d89e4c68897eef6a006 extends Template
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
        yield "<div id=\"auth-modal\" class=\"modal\">
    <div class=\"modal-content\">
        <span class=\"close\" onclick=\"hideAuthForm()\">&times;</span>
        <h2 id=\"auth-title\">Connexion</h2>
        <div id=\"auth-message\" class=\"\"></div>
        <form id=\"auth-form-element\">
            <div class=\"form-group\">
                <label for=\"email\">Email</label>
                <input type=\"email\" id=\"email\" name=\"email\" required>
            </div>
            <div class=\"form-group\">
                <label for=\"password\">Mot de passe</label>
                <input type=\"password\" id=\"password\" name=\"password\" required>
            </div>
            <div class=\"form-group remember-me\">
                <input type=\"checkbox\" id=\"remember\" name=\"remember\">
                <label for=\"remember\">Se souvenir de moi</label>
            </div>
            <div class=\"form-actions\">
                <button type=\"submit\">Valider</button>
                <button type=\"button\" onclick=\"hideAuthForm()\">Annuler</button>
            </div>
            <div class=\"form-links\">
                <a href=\"#\" onclick=\"showForgotPassword(); return false;\">Mot de passe oublié ?</a>
            </div>
        </form>
    </div>
</div>";
        return; yield '';
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "auth/auth-form.twig";
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
        return new Source("<div id=\"auth-modal\" class=\"modal\">
    <div class=\"modal-content\">
        <span class=\"close\" onclick=\"hideAuthForm()\">&times;</span>
        <h2 id=\"auth-title\">Connexion</h2>
        <div id=\"auth-message\" class=\"\"></div>
        <form id=\"auth-form-element\">
            <div class=\"form-group\">
                <label for=\"email\">Email</label>
                <input type=\"email\" id=\"email\" name=\"email\" required>
            </div>
            <div class=\"form-group\">
                <label for=\"password\">Mot de passe</label>
                <input type=\"password\" id=\"password\" name=\"password\" required>
            </div>
            <div class=\"form-group remember-me\">
                <input type=\"checkbox\" id=\"remember\" name=\"remember\">
                <label for=\"remember\">Se souvenir de moi</label>
            </div>
            <div class=\"form-actions\">
                <button type=\"submit\">Valider</button>
                <button type=\"button\" onclick=\"hideAuthForm()\">Annuler</button>
            </div>
            <div class=\"form-links\">
                <a href=\"#\" onclick=\"showForgotPassword(); return false;\">Mot de passe oublié ?</a>
            </div>
        </form>
    </div>
</div>", "auth/auth-form.twig", "C:\\Users\\ck_ri\\Developpement\\chaulacel\\calendrier.chaulacel\\templates\\auth\\auth-form.twig");
    }
}
