<?php

declare(strict_types=1);

namespace App\WordPress;

use App\IsPluginActive;
use Yard\Hook\Action;
use Yard\Hook\Filter;

class DisableComments
{
    /**
     * Removes the comments metabox from the classic editor.
     *
     * @param string $post_type The post type that metaboxes are being registered for.
     */
    #[Action("add_meta_boxes", 9999)]
    public static function remove_meta_boxes(string $post_type): void
    {
        remove_meta_box("commentsdiv", $post_type, "normal");
        remove_meta_box("commentstatusdiv", $post_type, "normal");
    }

    /**
     * Removes the comments node from the admin bar menu.
     *
     * @param \WP_Admin_Bar $wp_admin_bar An instance of the WP_Admin_Bar class.
     */

    #[Action("admin_bar_menu", 9999)]
    public static function remove_admin_bar_menu(
        \WP_Admin_Bar $wp_admin_bar,
    ): void {
        $wp_admin_bar->remove_node("comments");
    }

    /**
     * Removes blocks related to core/comments from the admin block selector.
     *
     * JavaScript is used to selectively remove blocks from the editor.
     * The PHP filter for allowed blocks passes ‘true’ to allow all blocks by default,
     * so you can’t get the full list of blocks and selectively remove them.
     * https://developer.wordpress.org/news/2024/01/how-to-disable-specific-blocks-in-wordpress/#disable-blocks-with-php
     */
    #[Action("admin_footer", 9999)]
    public static function remove_blocks(): void
    {
        echo "
			<script>
			if (typeof wp !== 'undefined' && typeof wp?.domReady === 'function') {
				wp.domReady(() => {
					if (typeof wp?.blocks?.unregisterBlockType === 'function') {
						const blocks = [
							'core/comments',
							'core/post-comments-form',
							'core/comments-query-loop',
							'core/latest-comments',
						];
						blocks.forEach((block) => {
							if (wp.blocks.getBlockType(block)) {
								wp.blocks.unregisterBlockType(block);
							}
						});
					}
				});
			}
			</script>";
    }

    /**
     * Redirects direct requests for the comments list and discussion settings page to the admin dashboard.
     */
    #[Action("admin_init", 0)]
    public static function redirect_to_admin_dashboard(): void
    {
        global $pagenow;

        if (
            \in_array(
                $pagenow,
                ["edit-comments.php", "options-discussion.php"],
                true,
            )
        ) {
            wp_safe_redirect(admin_url());
            exit();
        }
    }

    /**
     * Removes the Comments primary menu item and the Discussion submenu item (under Settings) from admin menus.
     */
    #[Action("admin_menu", 9999)]
    public static function remove_admin_menu(): void
    {
        remove_menu_page("edit-comments.php");
        remove_submenu_page("options-general.php", "options-discussion.php");
    }

    /**
     * Add actions and filters to run on the init hook.
     */
    #[Action("init", 9999)]
    public static function remove_comments_support(): void
    {
        // Removes post type support for comments and filters REST responses for each post type to remove comment support.
        foreach (get_post_types() as $post_type) {
            if (post_type_supports($post_type, "comments")) {
                remove_post_type_support($post_type, "comments");
            }

            // TODO
            // The REST API filters don't have a generic form, so they need to be registered for each post type.
            add_filter(
                "rest_prepare_{$post_type}",
                [self::class, "rest_prepare"],
                9999,
            );
        }

        // Removes the Akismet comments section from the dashboard.
        remove_action("rightnow_end", ["Akismet_Admin", "rightnow_stats"]);
    }

    /**
     * Filters REST responses for post endpoints to force comment_status to be closed.
     *
     * @param \WP_REST_Response $response Response to filter.
     *
     * @return \WP_REST_Response Filtered response.
     */
    public static function rest_prepare(
        \WP_REST_Response $response,
    ): \WP_REST_Response {
        $response->remove_link("replies");

        if (
            \is_array($response->data) &&
            isset($response->data["comment_status"])
        ) {
            $response->data["comment_status"] = "closed";
        }

        return $response;
    }

    /**
     * Filters whether comments are open to always return false for
     * unauthenticated users. This allows logged-in users to use the block notes
     * feature introduced in WordPress 6.9.
     *
     * @param bool $comments_open Whether the current post is open for comments.
     * @param int  $post_id       The post ID.
     */
    #[Filter("comments_open", 9999, 2)]
    public static function comments_open(
        bool $comments_open,
        int $post_id,
    ): bool {
        // Allow logged-in users to see comments.
        if (is_user_logged_in()) {
            return $comments_open;
        }

        return false;
    }

    /**
     * Short-circuits the comments query to return an empty array or 0 (if count
     * was requested). This allows logged-in users to use the block notes feature
     * introduced in WordPress 6.9.
     *
     * @param array<mixed>|int|null $comment_data  Not used.
     * @param \WP_Comment_Query     $comment_query The comment query object to filter results for.
     * @return int|array<mixed>|null Filtered comment data.
     */
    #[Filter("comments_pre_query", 9999, 2)]
    public static function comments_pre_query(
        $comment_data,
        \WP_Comment_Query $comment_query,
    ): int|array|null {
        // Allow logged-in users to see comments.
        if (is_user_logged_in()) {
            return $comment_data;
        }

        return $comment_query->query_vars["count"] ? 0 : [];
    }

    #[Filter("comments_rewrite_rules", 9999)]
    public static function comments_rewrite_rules(): array
    {
        return [];
    }

    /**
     * Filters the comment count to return zero for unauthenticated users. This
     * allows logged-in users to use the block notes feature introduced in
     * WordPress 6.9.
     *
     * @param string|int $comments_number A string representing the number of comments a post has, otherwise 0.
     * @param int        $post_id Post ID.
     *
     * @return int Filtered comment count.
     */
    #[Filter("get_comments_number", 9999, 2)]
    public static function get_comments_number(
        string|int $comments_number,
        int $post_id,
    ): int {
        // Allow logged-in users to see the comment count.
        if (is_user_logged_in()) {
            return (int) $comments_number;
        }

        return 0;
    }

    /**
     * Removes rewrite rules related to comments.
     *
     * @param array<string> $rules Rewrite rules to be filtered.
     *
     * @return array<string> Filtered rewrite rules.
     */
    #[Filter("rewrite_rules_array", 9999)]
    public static function rewrite_rules_array(array $rules): array
    {
        foreach ($rules as $regex => $rewrite) {
            if (str_contains($rewrite, 'cpage=$')) {
                unset($rules[$regex]);
            }
        }

        return $rules;
    }

    /**
     * Filters REST API endpoints to return an error response from comments
     * endpoints for unauthenticated users. This prevents comment botspam.
     *
     * @param mixed            $dispatch_result Dispatch result, will be used if not empty.
     * @param \WP_REST_Request $request         Request used to generate the response.
     * @param string           $route           Route matched for the request.
     * @param array<mixed>     $handler         Route handler used for the request.
     *
     * @return mixed Filtered REST API endpoints.
     */
    #[Filter("rest_dispatch_request", 9999, 4)]
    public static function rest_dispatch_request(
        mixed $dispatch_result,
        \WP_REST_Request $request,
        string $route,
        array $handler,
    ): mixed {
        $comment_routes = ["/wp/v2/comments", "/wp/v2/comments/(?P<id>[\d]+)"];

        if (!in_array($route, $comment_routes, true) || is_user_logged_in()) {
            return $dispatch_result;
        }

        return new \WP_Error(
            "rest_forbidden",
            __("Sorry, you are not allowed to access comments.", "alley"),
            [
                "status" => rest_authorization_required_code(),
            ],
        );
    }
}
