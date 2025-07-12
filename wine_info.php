<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Health Benefits of Wine</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <style>
    * {
      box-sizing: border-box;
    }

    body {
      font-family: 'Segoe UI', sans-serif;
      margin: 0;
      background: #fff8f0;
      color: #333;
    }

    header {
      background-color: #7b1e1e;
      color: #fff;
      padding: 30px 20px;
      text-align: center;
    }

    section {
      max-width: 1000px;
      margin: 30px auto;
      padding: 20px;
    }

    h2 {
      color: #7b1e1e;
      margin-top: 40px;
      text-align: center;
    }

    .cards {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
      justify-content: center;
    }

    .card {
      flex: 1 1 45%;
      background: #fff;
      padding: 20px;
      border-radius: 12px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
      text-align: center;
      transition: transform 0.3s ease;
    }

    .card:hover {
      transform: translateY(-5px);
    }

    .card i {
      font-size: 40px;
      color: #a52a2a;
      margin-bottom: 15px;
    }

    .card h3 {
      margin-bottom: 10px;
    }

    .card p {
      font-size: 16px;
      text-align: justify;
    }

    canvas {
      max-width: 100%;
      margin-top: 20px;
    }

    footer {
      background-color: #7b1e1e;
      color: #fff;
      text-align: center;
      padding: 15px;
    }

    /* Back Button Styles */
    .back-btn {
      display: inline-block;
      padding: 10px 20px;
      background-color: #7b1e1e;
      color: white;
      border: none;
      border-radius: 30px;
      text-decoration: none;
      font-size: 18px;
      text-align: center;
      margin: 20px auto;
      cursor: pointer;
      transition: background-color 0.3s ease, transform 0.3s ease;
    }

    .back-btn:hover {
      background-color: #a52a2a;
      transform: scale(1.05);
    }

    /* Media Queries for Responsiveness */
    @media (max-width: 768px) {
      .card {
        flex: 1 1 100%;
      }

      h1 {
        font-size: 24px;
      }

      .card i {
        font-size: 30px;
      }
    }

    @media (max-width: 480px) {
      header, section {
        padding: 15px;
      }

      .card p {
        font-size: 15px;
      }

      h2 {
        font-size: 20px;
      }
    }
  </style>
</head>
<body>

<header>
  <h1>🍷 Health Benefits of Wine</h1>
  <p>Explore how moderate wine consumption may positively impact health.</p>
</header>

<!-- Back Button -->
<a href="home.php" class="back-btn">← Back to Home</a>

<section>
  <h2>🩺 Scientifically Backed Benefits</h2>
  <div class="cards">
    <div class="card">
      <i class="fas fa-heartbeat"></i>
      <h3>Heart Health</h3>
      <p>Red wine contains polyphenols like resveratrol, which protect the heart's blood vessels, increase HDL (good cholesterol), and reduce the risk of blood clots and artery damage.</p>
    </div>
    <div class="card">
      <i class="fas fa-brain"></i>
      <h3>Cognitive Function</h3>
      <p>Antioxidants in wine help reduce inflammation and oxidative stress in the brain, potentially slowing cognitive decline and reducing the risk of Alzheimer’s and dementia.</p>
    </div>
    <div class="card">
      <i class="fas fa-vial"></i>
      <h3>Antioxidants</h3>
      <p>Red wine is rich in antioxidants like resveratrol and flavonoids, which protect cells from damage, support immunity, and fight chronic inflammation.</p>
    </div>
    <div class="card">
      <i class="fas fa-smile-beam"></i>
      <h3>Mood Booster</h3>
      <p>Moderate wine consumption can reduce stress and promote dopamine release, elevating mood and encouraging social interaction in relaxed settings.</p>
    </div>
  </div>
</section>

<section>
  <h2>📊 Antioxidant Comparison Across Beverages</h2>
  <p style="text-align:center;">Red wine offers significantly higher antioxidant content than other alcoholic beverages.</p>
  <canvas id="antioxidantChart"></canvas>
</section>

<section>
  <h2>🍇 Health Benefits of Resveratrol</h2>
  <p style="text-align:center;">A compound found in red wine grapes, resveratrol is known for its powerful biological effects.</p>
  <canvas id="resveratrolChart"></canvas>
</section>

<footer>
  <p>&copy; 2025 Wine Health Benefits. All rights reserved.</p>
</footer>

<script>
  const ctx1 = document.getElementById('antioxidantChart').getContext('2d');
  new Chart(ctx1, {
    type: 'bar',
    data: {
      labels: ['Red Wine', 'White Wine', 'Beer', 'Spirits'],
      datasets: [{
        label: 'Antioxidant Level (mg/100ml)',
        data: [3.2, 1.0, 0.5, 0.2],
        backgroundColor: ['#8B0000', '#FFA07A', '#FFD700', '#708090']
      }]
    },
    options: {
      responsive: true,
      scales: {
        y: {
          beginAtZero: true,
          title: { display: true, text: 'Antioxidant Level (mg)' }
        }
      }
    }
  });

  const ctx2 = document.getElementById('resveratrolChart').getContext('2d');
  new Chart(ctx2, {
    type: 'pie',
    data: {
      labels: ['Heart Protection', 'Anti-aging', 'Anti-inflammatory', 'Cancer Prevention'],
      datasets: [{
        data: [40, 25, 20, 15],
        backgroundColor: ['#b22222', '#e9967a', '#cd5c5c', '#f08080']
      }]
    },
    options: {
      responsive: true
    }
  });
</script>

</body>
</html>
