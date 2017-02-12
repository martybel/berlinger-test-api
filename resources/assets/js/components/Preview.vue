<template>
  <div id="previewModal" class="modal fade" aria-hidden="true" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Preview</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <img :src="'/1.0/media/' + uuid" class="picture--preview"/>
          <ul class="picture__info">
            <li v-for="(prop,label) in info">
              <label>{{ label }}</label>
              <a :href="prop" v-if="label === 'url'">{{ prop }}</a>
              <span v-else>{{ prop }}</span>
            </li>
          </ul>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>

  </div>
</template>
<script>
  export default {
    data() {
      return {
        info: {}
      };
    },

    props: ['uuid'],

    watch: {
      'uuid': function(val) {

        var self = this;

        axios.get('/1.0/media/info/' + val).then((response) => {
          if ( response.status === 200 ) {
            self.info = response.data;
          }
        });
      }
    },

  }
</script>