<?php
/**
 * =============================================================================
 * VERCEL SPEED INSIGHTS — Performance Monitoring
 * =============================================================================
 * 
 * This file provides Vercel Speed Insights integration for the application.
 * Speed Insights tracks real user performance metrics (Core Web Vitals) to help
 * understand and improve the application's performance.
 * 
 * USAGE:
 *   Include this file in the <head> or before </body> closing tag:
 *   <?php require_once VIEWS_COMPONENTS . '/includes/speed_insights.php'; ?>
 * 
 * METRICS TRACKED:
 *   - First Contentful Paint (FCP)
 *   - Largest Contentful Paint (LCP)
 *   - Cumulative Layout Shift (CLS)
 *   - First Input Delay (FID)
 *   - Interaction to Next Paint (INP)
 *   - Time to First Byte (TTFB)
 * 
 * NOTE:
 *   Speed Insights automatically detects the Vercel environment and only
 *   sends data when deployed on Vercel. No data is sent in local development.
 * 
 * =============================================================================
 */
?>
<script>
window.si = window.si || function () { (window.siq = window.siq || []).push(arguments); };
</script>
<script defer src="/_vercel/speed-insights/script.js"></script>
