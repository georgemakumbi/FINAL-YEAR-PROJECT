<?php
/**
 * =============================================================================
 * VERCEL SPEED INSIGHTS COMPONENT
 * =============================================================================
 * 
 * WHAT IS THIS?
 *   This component adds Vercel Speed Insights tracking to pages.
 *   Speed Insights measures real user performance metrics like:
 *   - First Contentful Paint (FCP)
 *   - Largest Contentful Paint (LCP)
 *   - First Input Delay (FID)
 *   - Cumulative Layout Shift (CLS)
 * 
 * HOW TO USE:
 *   Include this file in the <head> section of your HTML:
 *   <?php include VIEWS_COMPONENTS . '/includes/speed_insights.php'; ?>
 * 
 * REQUIREMENTS:
 *   1. Speed Insights must be enabled in your Vercel project dashboard
 *   2. After enabling, Vercel will serve the script from /_vercel/speed-insights/script.js
 *   3. This only works when deployed to Vercel (not in local development)
 * 
 * DOCUMENTATION:
 *   https://vercel.com/docs/speed-insights/quickstart
 * 
 * =============================================================================
 */
?>
<!-- Vercel Speed Insights -->
<script>
  window.si = window.si || function () { (window.siq = window.siq || []).push(arguments); };
</script>
<script defer src="/_vercel/speed-insights/script.js"></script>
