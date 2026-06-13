# 🛠️ Phase 6: Admin Dashboard Refactor & Real-Time Analytics

## What You'll Learn in This Phase

The admin dashboard is the control center of your voting system. 
Administrators use it to manage elections, candidates, students, and
view results. This phase teaches you about:

1. **Removing raw SQL from views** — Why template files should NEVER contain SQL
2. **Chart.js** — Creating interactive data visualizations
3. **Real-time dashboards** — Live-updating statistics
4. **The MVC Pattern in practice** — How Models feed data to Views

---

## Table of Contents

1. [The Problem with Raw SQL in Templates](#1-the-problem)
2. [Chart.js Data Visualization](#2-chartjs-data-visualization)
3. [Live-Updating Admin Dashboards](#3-live-updating-dashboards)
4. [Before vs After Code Comparison](#4-before-vs-after)

---

## 1. The Problem with Raw SQL in Templates

### Before (BAD — SQL mixed into the template):
```php
<!-- Inside the HTML template -->
<?php
$total_students = $conn->query("SELECT COUNT(*) as count FROM students")->fetch_assoc()['count'];
$total_candidates = $conn->query("SELECT COUNT(*) as count FROM candidates")->fetch_assoc()['count'];

// Results section — 15 lines of raw SQL!
$candidates_stmt = $conn->prepare("SELECT * FROM candidates WHERE position = ? ORDER BY votes DESC");
$candidates_stmt->bind_param("s", $pos);
$candidates_stmt->execute();
$candidates_by_pos = $candidates_stmt->get_result();
?>
```

### Problems:
1. **Messy**: SQL mixed with HTML is hard to read
2. **Duplicated**: Same queries written in multiple files
3. **Unsafe**: Easy to forget prepared statements
4. **Untestable**: Can't test the query without rendering HTML

### After (GOOD — Models handle all database logic):
```php
// Clean, readable, and ALL SQL is inside the Model class
$total_students   = Student::countAll($conn);
$total_voted      = Student::countVoted($conn);
$total_candidates = Candidate::countByStatus($conn, 'verified');
$results_data     = Candidate::getResults($conn);
```

---

## 2. Chart.js Data Visualization

### What is Chart.js?
A JavaScript library that turns data into beautiful, interactive charts.
We load it via CDN:
```html
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
```

### Creating a Horizontal Bar Chart:
```javascript
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['President', 'Secretary', 'Treasurer'],
        datasets: [{
            label: 'Votes',
            data: [240, 180, 95],
            backgroundColor: ['rgba(52,152,219,0.7)', 'rgba(39,174,96,0.7)', 'rgba(243,156,18,0.7)'],
        }]
    },
    options: {
        indexAxis: 'y',  // Makes it horizontal!
        responsive: true,
    }
});
```

### Creating a Doughnut Chart:
```javascript
new Chart(ctx, {
    type: 'doughnut',
    data: {
        labels: ['Voted', 'Not Yet Voted'],
        datasets: [{
            data: [45, 55],
            backgroundColor: ['rgba(39,174,96,0.8)', 'rgba(189,195,199,0.5)'],
        }]
    },
    options: {
        cutout: '65%',  // Size of the hole in the middle
    }
});
```

---

## 3. Live-Updating Dashboards

### How It Works:
```javascript
// Every 15 seconds, fetch fresh data from the API
setInterval(async () => {
    const res = await fetch('api/analytics.php');
    const data = await res.json();
    
    // Update the DOM with new values
    document.getElementById('stat-votes').textContent = data.overview.total_votes_cast;
    document.getElementById('stat-turnout').textContent = data.overview.voter_turnout + '%';
}, 15000);
```

This means the admin sees vote counts update in real-time during an election!

---

## 4. Before vs After

### Dashboard Statistics:
```diff
-$total_students = $conn->query("SELECT COUNT(*) as count FROM students")->fetch_assoc()['count'];
-$total_candidates = $conn->query("SELECT COUNT(*) as count FROM candidates")->fetch_assoc()['count'];
-$total_votes = $conn->query("SELECT COUNT(*) as count FROM votes")->fetch_assoc()['count'];
-$voters_who_voted = $conn->query("SELECT COUNT(*) as count FROM students WHERE has_voted = 1")->fetch_assoc()['count'];
-$turnout = $total_students > 0 ? round(($voters_who_voted / $total_students) * 100, 1) : 0;
+$total_students   = Student::countAll($conn);
+$total_voted      = Student::countVoted($conn);
+$total_candidates = Candidate::countByStatus($conn, 'verified');
+$total_votes      = Vote::countAll($conn);
+$turnout          = Student::getVoterTurnout($conn);
```

### Results Section:
```diff
-$positions = $conn->query("SELECT DISTINCT position FROM candidates");
-while ($position = $positions->fetch_assoc()):
-    $candidates_stmt = $conn->prepare("SELECT * FROM candidates WHERE position = ? ORDER BY votes DESC");
-    $candidates_stmt->bind_param("s", $pos);
-    $candidates_stmt->execute();
-    $candidates_by_pos = $candidates_stmt->get_result();
-    $total_stmt = $conn->prepare("SELECT SUM(votes) as total FROM candidates WHERE position = ?");
-    // ... 25+ more lines of raw SQL ...
+$results_data = Candidate::getResults($conn);
+$grouped = [];
+foreach ($results_data as $row) {
+    $grouped[$row['position']][] = $row;
+}
```

---

## Files Modified

```
MOD:  views/admin/admin_dashboard.php  ← Complete refactor (raw SQL → Models)
NEW:  docs/PHASE6_ADMIN_DASHBOARD.md   ← This learning guide
```

## Key Takeaway for Your Defense

When your panel asks "How does the admin dashboard work?", you can say:

> "The dashboard follows MVC architecture. The controller layer loads
> data from Model classes — Student::countAll(), Vote::getStatsByPosition(),
> Candidate::getResults(). The view layer renders this data using HTML
> and Chart.js for visualization. A JSON API endpoint (api/analytics.php)
> enables real-time updates via the Fetch API, refreshing statistics
> every 15 seconds without page reloads."
