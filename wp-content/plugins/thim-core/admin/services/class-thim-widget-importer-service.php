<?php

/**
 * Class Thim_Widget_Importer_Service
 *
 * @since 0.4.0
 */

if ( ! class_exists( 'Thim_Widget_Importer_Service' ) ) {

	class Thim_Widget_Importer_Service {
		/**
		 * Remove all widgets
		 *
		 * @since 0.8.5
		 */
		public static function remove_all() {
			self::remove_inactive_widgets();
			self::remove_active_widgets();
		}

		/**
		 * Remove active widgets.
		 *
		 * @since 0.8.5
		 */
		private static function remove_active_widgets() {
			$sidebars_widgets = wp_get_sidebars_widgets();

			foreach ( $sidebars_widgets as $sidebar_key => $sidebars_widget ) {
				if ( $sidebar_key === 'wp_inactive_widgets' ) {
					continue;
				}

				foreach ( $sidebars_widget as $key => $widget_id ) {
					$pieces       = explode( '-', $widget_id );
					$multi_number = array_pop( $pieces );
					$id_base      = implode( '-', $pieces );
					$widget       = get_option( 'widget_' . $id_base );
					unset( $widget[ $multi_number ] );
					update_option( 'widget_' . $id_base, $widget );
					unset( $sidebars_widgets[ $sidebar_key ][ $key ] );
				}
			}

			wp_set_sidebars_widgets( $sidebars_widgets );
		}

		/**
		 * Remove inactive widgets.
		 *
		 * @since 0.8.5
		 */
		private static function remove_inactive_widgets() {
			$sidebars_widgets = wp_get_sidebars_widgets();

			foreach ( $sidebars_widgets['wp_inactive_widgets'] as $key => $widget_id ) {
				$pieces       = explode( '-', $widget_id );
				$multi_number = array_pop( $pieces );
				$id_base      = implode( '-', $pieces );
				$widget       = get_option( 'widget_' . $id_base );
				unset( $widget[ $multi_number ] );
				update_option( 'widget_' . $id_base, $widget );
				unset( $sidebars_widgets['wp_inactive_widgets'][ $key ] );
			}

			wp_set_sidebars_widgets( $sidebars_widgets );
		}

		/**
		 * Import widgets.
		 *
		 * @param $json_data
		 * @param $widget_logic
		 * @param $clear_current
		 *
		 * @sine 0.4.0
		 */
		public function import( $json_data, $widget_logic, $clear_current = true ) {
			global $wp_registered_sidebars;

			//	Clear current widgets.
			if ( $clear_current ) {
				self::clear_widgets();
			}

			$sidebars_data  = $json_data[0];
			$widget_data    = $json_data[1];
			$new_widgets    = array();
			$map_widgets_id = array();


			foreach ( $sidebars_data as $import_sidebar => $import_widgets ) {
				foreach ( $import_widgets as $import_widget ) {
					//if the sidebar exists
					if ( isset( $wp_registered_sidebars[ $import_sidebar ] ) ) {
						$title               = trim( substr( $import_widget, 0, strrpos( $import_widget, '-' ) ) );
						$index               = trim( substr( $import_widget, strrpos( $import_widget, '-' ) + 1 ) );
						$current_widget_data = get_option( 'widget_' . $title );
						$new_widget_name     = self::get_new_widget_name( $title, $index );
						$new_index           = trim( substr( $new_widget_name, strrpos( $new_widget_name, '-' ) + 1 ) );
						if ( ! empty( $new_widgets[ $title ] ) && is_array( $new_widgets[ $title ] ) ) {
							while ( array_key_exists( $new_index, $new_widgets[ $title ] ) ) {
								$new_index ++;
							}
						}
						$current_sidebars[ $import_sidebar ][]   = $title . '-' . $new_index;
						$map_widgets_id[ $title . '-' . $index ] = $title . '-' . $new_index;
						if ( array_key_exists( $title, $new_widgets ) ) {
							$new_widgets[ $title ][ $new_index ] = $widget_data[ $title ][ $index ];
							$multiwidget                         = $new_widgets[ $title ]['_multiwidget'];
							unset( $new_widgets[ $title ]['_multiwidget'] );
							$new_widgets[ $title ]['_multiwidget'] = isset( $multiwidget ) ? $multiwidget : 0;
						} else {
							$current_widget_data[ $new_index ] = $widget_data[ $title ][ $index ];
							$current_multiwidget               = $current_widget_data['_multiwidget'];
							$new_multiwidget                   = isset( $widget_data[ $title ]['_multiwidget'] ) ? $widget_data[ $title ]['_multiwidget'] : false;
							$multiwidget                       = ( $current_multiwidget != $new_multiwidget ) ? $current_multiwidget : 1;
							unset( $current_widget_data['_multiwidget'] );
							$current_widget_data['_multiwidget'] = isset( $multiwidget ) ? $multiwidget : 0;
							$new_widgets[ $title ]               = $current_widget_data;
						}
					}
				}
			}

			// Import widget.
			if ( isset( $new_widgets ) && isset( $current_sidebars ) ) {
				update_option( 'sidebars_widgets', $current_sidebars );
				foreach ( $new_widgets as $title => $content ) {
					update_option( 'widget_' . $title, $content );
				}
			}

			// Import widget logic options.
			self::import_widget_logic( $widget_logic, $map_widgets_id );
			self::mapping_nav_menu();
			self::mapping_mega_menu();
		}

		/**
		 * Remap nav menu
		 *
		 * @since 0.5.0
		 */
		private function mapping_nav_menu() {
			$nav_menus = get_option( 'widget_nav_menu' );

			if ( empty( $nav_menus ) ) {
				return;
			}

			$thim_wp_import = new Thim_WP_Import_Service( false );
			$terms          = $thim_wp_import->processed_terms;

			if ( empty( $terms ) ) {
				return;
			}

			foreach ( $nav_menus as $key => $nav_menu ) {
				$nav_menu_id = ! empty( $nav_menu['nav_menu'] ) ? $nav_menu['nav_menu'] : false;

				if ( ! $nav_menu_id ) {
					continue;
				}

				if ( empty( $terms[ $nav_menu_id ] ) ) {
					continue;
				}

				$new_nav_menu_id = $terms[ $nav_menu_id ];
				$nav_menu_obj    = wp_get_nav_menu_object( $new_nav_menu_id );

				if ( ! $nav_menu_obj ) {
					continue;
				}

				$nav_menu['nav_menu'] = $new_nav_menu_id;
				$nav_menus[ $key ]    = $nav_menu;
			}

			update_option( 'widget_nav_menu', $nav_menus );
		}


		/**
		 *
		 * @param string $widget_name
		 * @param string $widget_index
		 *
		 * @since 0.4.0
		 *
		 * @return string
		 */
		function get_new_widget_name( $widget_name, $widget_index ) {
			$current_sidebars = get_option( 'sidebars_widgets' );
			$all_widget_array = array();
			foreach ( $current_sidebars as $sidebar => $widgets ) {
				if ( ! empty( $widgets ) && is_array( $widgets ) && $sidebar != 'wp_inactive_widgets' ) {
					foreach ( $widgets as $widget ) {
						$all_widget_array[] = $widget;
					}
				}
			}
			while ( in_array( $widget_name . '-' . $widget_index, $all_widget_array ) ) {
				$widget_index ++;
			}
			$new_widget_name = $widget_name . '-' . $widget_index;

			return $new_widget_name;
		}


		/**
		 * Clear all widgets before import.
		 *
		 * @since 0.4.0
		 */
		function clear_widgets() {
			$sidebars = wp_get_sidebars_widgets();
			$inactive = isset( $sidebars['wp_inactive_widgets'] ) ? $sidebars['wp_inactive_widgets'] : array();
			unset( $sidebars['wp_inactive_widgets'] );
			foreach ( $sidebars as $sidebar => $widgets ) {
				$inactive             = array_merge( $inactive, $widgets );
				$sidebars[ $sidebar ] = array();
			}
			$sidebars['wp_inactive_widgets'] = $inactive;
			wp_set_sidebars_widgets( $sidebars );
		}


		/**
		 * Import widget logic options.
		 *
		 * @param $file
		 *
		 * @since 0.4.0
		 */
		function import_widget_logic( $file, $map ) {
			if ( is_file( $file ) ) {
				$import = explode( "\n", file_get_contents( $file ) );
				if ( trim( array_shift( $import ) ) == "[START=WIDGET LOGIC OPTIONS]" && trim( array_pop( $import ) ) == "[STOP=WIDGET LOGIC OPTIONS]" ) {
					foreach ( $import as $import_option ) {
						list( $key, $value ) = explode( "\t", $import_option );
						$wl_options[ $map[ $key ] ] = json_decode( $value );
					}
				}
				update_option( 'widget_logic', $wl_options );
			}
		}

		/**
		 * Mapping item mega menu.
		 *
		 * @since 0.6.0
		 */
		private function mapping_mega_menu() {
			global $wpdb;
			$query = "SELECT option_name FROM {$wpdb->prefix}options WHERE option_name LIKE 'widget_%'";
			$rows  = $wpdb->get_results( $query, ARRAY_A );

			if ( count( $rows ) === 0 ) {
				return;
			}

			$thim_wp_import = new Thim_WP_Import_Service( false );
			$menu_items     = $thim_wp_import->processed_menu_items;

			if ( empty( $menu_items ) ) {
				return;
			}

			foreach ( $rows as $row ) {
				$is_update   = false;
				$widget_name = $row['option_name'];

				$widget_option = get_option( $widget_name, false );
				if ( ! $widget_option || ! is_array( $widget_option ) ) {
					continue;
				}

				$widget_new_option = $widget_option;

				foreach ( $widget_option as $key => $value ) {
					if ( ! is_array( $value ) ) {
						continue;
					}

					//Mapping parent id
					$parent_menu_id = isset( $value['mega_menu_parent_menu_id'] ) ? $value['mega_menu_parent_menu_id'] : false;
					if ( ! $parent_menu_id ) {
						continue;
					}

					$new_parent_menu_id = ! empty( $menu_items[ '' . $parent_menu_id ] ) ? $menu_items[ '' . $parent_menu_id ] : false;
					if ( ! $new_parent_menu_id ) {
						continue;
					}

					$value['mega_menu_parent_menu_id'] = $new_parent_menu_id;

					$menu_orders = isset( $value['mega_menu_order'] ) ? $value['mega_menu_order'] : false;

					if ( $menu_orders && is_array( $menu_orders ) ) {
						$new_menu_orders = array();
						foreach ( $menu_orders as $k => $v ) {
							$new_menu_orders[ $new_parent_menu_id ] = $v;
						}

						$value['mega_menu_order'] = $new_menu_orders;
					}

					$widget_new_option[ $key ] = $value;
					$is_update                 = true;
				}

				if ( $is_update ) {
					update_option( $widget_name, $widget_new_option );
				}
			}
		}

	}
}