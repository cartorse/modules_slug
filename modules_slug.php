<?php
/**
 * modules_slug — Muestra el nombre de carpeta (nombre técnico) en el listado de módulos.
 *
 * @author cartorse
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class Modules_Slug extends Module
{
    const CONFIG_CARD_SELECTOR = 'MODULES_SLUG_CARD_SELECTOR';
    const CONFIG_NAME_SELECTOR = 'MODULES_SLUG_NAME_SELECTOR';

    const DEFAULT_CARD_SELECTOR = '.module-item[data-tech-name]';
    const DEFAULT_NAME_SELECTOR = 'h3.module-name-list';

    public function __construct()
    {
        $this->name = 'modules_slug';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'cartorse';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Module Folder Name');
        $this->description = $this->l('Muestra el nombre de la carpeta (nombre técnico) de cada módulo en el listado de módulos.');
        $this->ps_versions_compliancy = ['min' => '9.0.0', 'max' => _PS_VERSION_];
    }

    public function install()
    {
        return parent::install()
            && $this->registerHook('displayBackOfficeHeader')
            && Configuration::updateValue(self::CONFIG_CARD_SELECTOR, self::DEFAULT_CARD_SELECTOR)
            && Configuration::updateValue(self::CONFIG_NAME_SELECTOR, self::DEFAULT_NAME_SELECTOR);
    }

    public function uninstall()
    {
        Configuration::deleteByName(self::CONFIG_CARD_SELECTOR);
        Configuration::deleteByName(self::CONFIG_NAME_SELECTOR);

        return parent::uninstall();
    }

    public function getContent()
    {
        $output = '';

        if (Tools::isSubmit('submit_modules_slug')) {
            $cardSelector = trim(Tools::getValue(self::CONFIG_CARD_SELECTOR));
            $nameSelector = trim(Tools::getValue(self::CONFIG_NAME_SELECTOR));

            if (empty($cardSelector) || empty($nameSelector)) {
                $output .= $this->displayError($this->l('Ambos selectores son obligatorios.'));
            } else {
                Configuration::updateValue(self::CONFIG_CARD_SELECTOR, $cardSelector);
                Configuration::updateValue(self::CONFIG_NAME_SELECTOR, $nameSelector);
                $output .= $this->displayConfirmation($this->l('Configuración guardada.'));
            }
        }

        return $output . $this->renderForm();
    }

    protected function renderForm()
    {
        $helper = new HelperForm();
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
        $helper->default_form_language = (int) Configuration::get('PS_LANG_DEFAULT');
        $helper->submit_action = 'submit_modules_slug';
        $helper->title = $this->displayName;

        $fields_form = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Selectores CSS'),
                    'icon'  => 'icon-cogs',
                ],
                'description' => $this->l('Selectores CSS usados para localizar las tarjetas de módulo y el elemento donde insertar el nombre de carpeta. Usa selectores en cascada si el selector simple no es suficiente.'),
                'input' => [
                    [
                        'type'     => 'text',
                        'label'    => $this->l('Selector de tarjeta'),
                        'name'     => self::CONFIG_CARD_SELECTOR,
                        'size'     => 80,
                        'required' => true,
                        'desc'     => $this->l('Elemento raíz de cada módulo que contiene data-tech-name. Por defecto: ' . self::DEFAULT_CARD_SELECTOR),
                    ],
                    [
                        'type'     => 'text',
                        'label'    => $this->l('Selector del nombre'),
                        'name'     => self::CONFIG_NAME_SELECTOR,
                        'size'     => 80,
                        'required' => true,
                        'desc'     => $this->l('Elemento dentro de la tarjeta donde se insertará el nombre técnico. Por defecto: ' . self::DEFAULT_NAME_SELECTOR),
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Guardar'),
                ],
                'buttons' => [
                    [
                        'title' => $this->l('Restaurar valores por defecto'),
                        'icon'  => 'process-icon-refresh',
                        'name'  => 'reset_modules_slug',
                        'type'  => 'submit',
                        'class' => 'btn btn-default pull-right',
                    ],
                ],
            ],
        ];

        // Restaurar defaults si se pulsó el botón
        if (Tools::isSubmit('reset_modules_slug')) {
            Configuration::updateValue(self::CONFIG_CARD_SELECTOR, self::DEFAULT_CARD_SELECTOR);
            Configuration::updateValue(self::CONFIG_NAME_SELECTOR, self::DEFAULT_NAME_SELECTOR);
        }

        $helper->fields_value[self::CONFIG_CARD_SELECTOR] = Configuration::get(self::CONFIG_CARD_SELECTOR);
        $helper->fields_value[self::CONFIG_NAME_SELECTOR] = Configuration::get(self::CONFIG_NAME_SELECTOR);

        return $helper->generateForm([$fields_form]);
    }

    public function hookDisplayBackOfficeHeader()
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '';
        if (strpos($uri, 'module') === false) {
            return '';
        }

        $cardSelector = pSQL(Configuration::get(self::CONFIG_CARD_SELECTOR) ?: self::DEFAULT_CARD_SELECTOR);
        $nameSelector = pSQL(Configuration::get(self::CONFIG_NAME_SELECTOR) ?: self::DEFAULT_NAME_SELECTOR);

        return '<style>
            .module-tech-name {
                display: block;
                font-size: 11px;
                color: #aaa;
                font-weight: normal;
                font-family: monospace;
                margin-top: 2px;
            }
            </style>
            <script>
            (function () {
                var CARD = ' . json_encode($cardSelector) . ';
                var NAME = ' . json_encode($nameSelector) . ';

                function labelModules() {
                    document.querySelectorAll(CARD).forEach(function (el) {
                        if (el.dataset.slugLabeled) return;
                        el.dataset.slugLabeled = "1";
                        var techName = el.dataset.techName;
                        var nameEl = el.querySelector(NAME);
                        if (nameEl && techName) {
                            var badge = document.createElement("small");
                            badge.className = "module-tech-name";
                            badge.textContent = techName;
                            nameEl.appendChild(badge);
                        }
                    });
                }

                var observer = new MutationObserver(labelModules);
                document.addEventListener("DOMContentLoaded", function () {
                    labelModules();
                    observer.observe(document.body, { childList: true, subtree: true });
                });
                if (document.readyState !== "loading") {
                    labelModules();
                    observer.observe(document.body, { childList: true, subtree: true });
                }
            }());
            </script>';
    }
}
