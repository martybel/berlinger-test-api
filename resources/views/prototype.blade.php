<html>
  <head>
    <title>Berlinger Picture Test</title>
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="_token" content="{{ csrf_token() }}"/>
    <script>window.Laravel = { csrfToken: '{{ csrf_token() }}' }</script>
    <link href="css/app.css" type="text/css" rel="stylesheet" />
  </head>
  <body>
    <div id="app">
      <app token="{{ csrf_token() }}"></app>
    </div>
    <div class="row">
      <div class="container">
        <h1>Berlinger test prototype</h1>
        <p>This is a working prototype for the Berlinger Interview test.</p>
        <p>I had to make a few assumptions so here they are:</p>
        <ul>
          <li>You will probably throw a large set at this</li>
          <li>There is no enclosure token defined, so none is set</li>
          <li>No validity check is done on the image itself, if the URL is valid, it will show</li>
          <li>To prevent lag/timeouts, import is done in a background job</li>
          <li>Only valid entries will be shown by default</li>
          <li>To prevent too big a file, i only load the first 1000 entries</li>
        </ul>
      </div>
    </div>
    <script type="text/javascript" src="/js/app.js"></script>
  </body>
</html>
