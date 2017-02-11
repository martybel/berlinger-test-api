<template>
  <div class="row">
    <div class="container">
      <fieldset>

      <div v-if="!batch">
        <h1>Upload your CSV</h1>
        <form id="uploadForm" method="post" enctype="multipart/form-data" action="/1.0/csv/upload" @submit.prevent="postSubmit">
          <input type="hidden" name="_token" :value="token"/>
          <input type="file" name="csv" />
          <button type="submit">Send File</button>
        </form>
      </div>
      <div v-else>
        <h1>Upload complete</h1>
        <p>Your file is being processed. You batch id is {{ batch }}</p>
      </div>
      </fieldset>
    </div>
  </div>
</template>
<script>
  export default {
    methods: {
      postSubmit: function()
      {
        const config = { headers: { 'Content-Type': 'multipart/form-data' } };

        var form = new FormData();
        form.append('_token', $('#uploadForm [name="_token"]').val());
        console.log($('#uploadForm [name="csv"]').files);
        form.append('csv',$('#uploadForm [name="csv"]')[0].files[0]);

        axios.post('/1.0/csv/upload',form,config).then((response) => {

          if ( response.status === 200 ) {
            this.batch = response.data.batch;
          }
        });
      }

    },

    props: [ 'token'],

    data: function() {
      return {
        'batch': false
      };
    },

    mounted() {
      console.log(window.Laravel);
//      $('input[name="_token"]').val(window.Laravel.csrfToken);
    }

  }
</script>
