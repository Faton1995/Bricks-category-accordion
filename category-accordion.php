<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Direktzugriff verhindern

class MyPrefix_Element_Category_Accordion extends \Bricks\Element {
    // Element-Eigenschaften
    public $category     = 'general'; // Element-Kategorie
    public $name         = 'myprefix-category-accordion'; // Eindeutiger Elementname mit Präfix
    public $icon         = 'fa-solid fa-sitemap'; // Icon für den Builder
    public $css_selector = '.category-accordion'; // Standard-CSS-Selektor
    public $scripts      = []; // Skripte, die geladen werden sollen
    public $nestable     = true; // Nestable Element

    // Konstruktor (optional)
    public function __construct() {
        parent::__construct();
    }

    // Element-Label im Builder
    public function get_label() {
        return esc_html__( 'Kategorie Akkordeon', 'bricks' );
    }

    // Schlüsselwörter für die Suche im Builder
    public function get_keywords() {
        return [ 'category', 'accordion', 'taxonomy', 'navigation' ];
    }

    // Kontrollgruppen definieren
    public function set_control_groups() {
        $this->control_groups['general'] = [
            'title' => esc_html__( 'Allgemein', 'bricks' ),
            'tab'   => 'content',
        ];
        $this->control_groups['styles'] = [
            'title' => esc_html__( 'Stile', 'bricks' ),
            'tab'   => 'style',
        ];
        $this->control_groups['typography'] = [
            'title' => esc_html__( 'Typografie', 'bricks' ),
            'tab'   => 'style',
        ];
        $this->control_groups['toggle_icons'] = [
            'title' => esc_html__( 'Toggle Icons', 'bricks' ),
            'tab'   => 'content',
        ];
    }

    // Kontrollen definieren
    public function set_controls() {
        // Allgemeine Einstellungen
        $this->controls['taxonomy'] = [
            'label'   => esc_html__( 'Taxonomie', 'bricks' ),
            'type'    => 'select',
            'options' => $this->get_taxonomies(),
            'default' => 'category',
            'group'   => 'general',
            'tab'     => 'content',
        ];

        $this->controls['parent_term'] = [
            'label'       => esc_html__( 'Eltern-Term-ID', 'bricks' ),
            'type'        => 'number',
            'default'     => 0,
            'placeholder' => esc_html__( '0 für oberste Ebene', 'bricks' ),
            'group'       => 'general',
            'tab'         => 'content',
        ];

        // Toggle Icons
        $this->controls['toggle_icon_closed'] = [
            'label'   => esc_html__( 'Icon geschlossen', 'bricks' ),
            'type'    => 'icon',
            'default' => 'fa-solid fa-chevron-right',
            'group'   => 'toggle_icons',
            'tab'     => 'content',
        ];

        $this->controls['toggle_icon_open'] = [
            'label'   => esc_html__( 'Icon geöffnet', 'bricks' ),
            'type'    => 'icon',
            'default' => 'fa-solid fa-chevron-down',
            'group'   => 'toggle_icons',
            'tab'     => 'content',
        ];

        // Stileinstellungen
        $this->controls['list_style'] = [
            'label'   => esc_html__( 'Listenstil', 'bricks' ),
            'type'    => 'select',
            'options' => [
                'none'    => esc_html__( 'Keine', 'bricks' ),
                'disc'    => esc_html__( 'Punkt', 'bricks' ),
                'circle'  => esc_html__( 'Kreis', 'bricks' ),
                'square'  => esc_html__( 'Quadrat', 'bricks' ),
                'decimal' => esc_html__( 'Zahlen', 'bricks' ),
            ],
            'default' => 'none',
            'css'     => [
                [
                    'property' => 'list-style-type',
                    'selector' => '.category-accordion ul',
                ],
            ],
            'group'   => 'styles',
            'tab'     => 'style',
        ];

        $this->controls['indent'] = [
            'label'   => esc_html__( 'Einrückung', 'bricks' ),
            'type'    => 'slider',
            'units'   => [ 'px', 'em', 'rem' ],
            'default' => [
                'size' => 20,
                'unit' => 'px',
            ],
            'css'     => [
                [
                    'property' => 'padding-left',
                    'selector' => '.category-accordion ul ul',
                ],
            ],
            'group'   => 'styles',
            'tab'     => 'style',
        ];

        // Typografie
        $this->controls['link_typography'] = [
            'label' => esc_html__( 'Link Typografie', 'bricks' ),
            'type'  => 'typography',
            'css'   => [
                [
                    'property' => 'typography',
                    'selector' => '.category-accordion li a',
                ],
            ],
            'group' => 'typography',
            'tab'   => 'style',
        ];

        // Farben
        $this->controls['link_color'] = [
            'label'   => esc_html__( 'Link Farbe', 'bricks' ),
            'type'    => 'color',
            'default' => '#333333',
            'css'     => [
                [
                    'property' => 'color',
                    'selector' => '.category-accordion li a',
                ],
            ],
            'group'   => 'typography',
            'tab'     => 'style',
        ];

        $this->controls['link_hover_color'] = [
            'label'   => esc_html__( 'Link Hover Farbe', 'bricks' ),
            'type'    => 'color',
            'default' => '#0073aa',
            'css'     => [
                [
                    'property' => 'color',
                    'selector' => '.category-accordion li a:hover',
                ],
            ],
            'group'   => 'typography',
            'tab'     => 'style',
        ];
    }

    // Hilfsfunktion zum Abrufen öffentlicher Taxonomien
    private function get_taxonomies() {
        $taxonomies = get_taxonomies( [ 'public' => true ], 'objects' );
        $options = [];
        foreach ( $taxonomies as $taxonomy ) {
            $options[ $taxonomy->name ] = $taxonomy->labels->singular_name;
        }
        return $options;
    }

    // Nestable Item definieren
    public function get_nestable_item( $term ) {
        $has_children = get_terms( [
            'taxonomy'   => $term->taxonomy,
            'parent'     => $term->term_id,
            'hide_empty' => false,
            'fields'     => 'ids',
        ] );

        $children = [];

        if ( ! empty( $has_children ) && ! is_wp_error( $has_children ) ) {
            foreach ( $has_children as $child_id ) {
                $child_term = get_term( $child_id, $term->taxonomy );
                if ( $child_term && ! is_wp_error( $child_term ) ) {
                    $children[] = $this->get_nestable_item( $child_term );
                }
            }
        }

        $item = [
            'name'     => 'block',
            'label'    => esc_html( $term->name ),
            'settings' => [
                '_cssClasses' => 'category-item',
            ],
            'children' => [
                [
                    'name'     => 'text',
                    'settings' => [
                        'text' => esc_html( $term->name ),
                    ],
                ],
            ],
        ];

        if ( ! empty( $children ) ) {
            $item['children'][] = [
                'name'     => 'block',
                'settings' => [
                    '_cssClasses' => 'children',
                ],
                'children' => $children,
            ];
        }

        return $item;
    }

    // Nestable Children definieren
    public function get_nestable_children() {
        $settings    = $this->settings;
        $taxonomy    = ! empty( $settings['taxonomy'] ) ? $settings['taxonomy'] : 'category';
        $parent_term = isset( $settings['parent_term'] ) ? intval( $settings['parent_term'] ) : 0;

        $terms = get_terms( [
            'taxonomy'   => $taxonomy,
            'parent'     => $parent_term,
            'hide_empty' => false,
        ] );

        $children = [];

        if ( $terms && ! is_wp_error( $terms ) ) {
            foreach ( $terms as $term ) {
                $children[] = $this->get_nestable_item( $term );
            }
        }

        return $children;
    }

    // Skripte und Styles einbinden
    public function enqueue_scripts() {
        wp_enqueue_style( 'category-accordion-css', get_stylesheet_directory_uri() . '/assets/css/category-accordion.css' );
        wp_enqueue_script( 'category-accordion-js', get_stylesheet_directory_uri() . '/assets/js/category-accordion.js', [ 'jquery' ], null, true );
    }

    // Element rendern
    public function render() {
        $settings = $this->settings;

        // Attribute setzen
        $this->set_attribute( '_root', 'class', 'category-accordion' );

        // Datenattribute für Icons
        $icon_closed = ! empty( $settings['toggle_icon_closed'] ) ? $settings['toggle_icon_closed'] : 'fa-solid fa-chevron-right';
        $icon_open   = ! empty( $settings['toggle_icon_open'] ) ? $settings['toggle_icon_open'] : 'fa-solid fa-chevron-down';

        $this->set_attribute( '_root', 'data-icon-closed', esc_attr( $icon_closed ) );
        $this->set_attribute( '_root', 'data-icon-open', esc_attr( $icon_open ) );

        // Skripte und Styles einbinden
        $this->enqueue_scripts();

        // Ausgabe generieren
        $output = '<div ' . $this->render_attributes( '_root' ) . '>';
        $output .= Frontend::render_children( $this );
        $output .= '</div>';

        echo $output;
    }
}