<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Shader Book — About</title>
    <link rel="stylesheet" href="styles.css">
  </head>
  <body>
    <?php include __DIR__ . '/header.php'; ?>
    <main class="container">
      <section>
        <h2>Overview</h2>
        <p>This site is a learning resource about shaders. This page describes the project goals and the planned structure.</p>
      </section>
      
      <section style="margin-top: 2rem;">
        <h2>Technologies Used</h2>
        <p>This website utilizes various technologies for graphics programming and web development. OpenGL is a core technology used for shader development and graphics rendering.</p>
        <div style="display: flex; align-items: center; gap: 2rem; flex-wrap: wrap; margin-top: 1.5rem;">
          <!-- Large OpenGL logo display -->
          <div style="text-align: center;">
            <img src="media/opengl_logo.svg" alt="OpenGL Logo - Large" width="300" height="300" style="display: block; margin: 0 auto;">
            <p style="margin-top: 0.5rem; font-size: 0.9rem; color: #98a6bc;">Large (300×300px)</p>
          </div>
          
          <!-- Small OpenGL logo display -->
          <div style="text-align: center;">
            <img src="media/opengl_logo_small.svg" alt="OpenGL Logo - Small" width="150" height="150" style="display: block; margin: 0 auto;">
            <p style="margin-top: 0.5rem; font-size: 0.9rem; color: #98a6bc;">Small (150×150px)</p>
          </div>
        </div>
      </section>
    </main>
    <footer class="site-footer">
      <div class="container">
        <p>&copy; <span id="year"></span> Shader Book</p>
      </div>
    </footer>
    <script>document.getElementById('year').textContent = new Date().getFullYear();</script>
  </body>
  </html>

