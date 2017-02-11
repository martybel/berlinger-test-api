<template>
  <div class="row">
    <div class="container">
      <div class="media__filter">
        <label>Filter</label>
        <select v-model="status" @change="reloadMedia">
          <option value="200">Valid pictures</option>
          <option value="404">Not found</option>
          <option value="400">Invalid</option>
        </select>
      </div>
      <div class="media-table">
        <table>
          <col width="30%">
          <col width="30%">
          <col width="30%">
          <col width="10%">
          <thead>
          <th>UUID</th>
          <th>Title</th>
          <th>Location</th>
          <th>Actions</th>
          </thead>
          <tbody>
          <tr v-for="row in media">
            <td class="uuid">{{ row.uuid }}</td>
            <td>{{ row.title }}</td>
            <td>{{ row.url }}</td>
            <td><button class="btn btn-default" @click="showImage(row.uuid)">View</button></td>
          </tr>
          </tbody>
        </table>
      </div>
    </div>
    <preview :uuid="selected"></preview>
  </div>
</template>
<script>
  import Preview from './Preview.vue'

  export default {
    data: function() {
      return {
        media: [],
        selected: false,
        status: 200
      };
    },

    components: {
      preview:  Preview
    },

    mounted() {
      var self = this;

      axios.get('/1.0/media').then(function(response) {
        if ( response.status === 200 ) {
          self.media = response.data;
        }
      });
    },

    methods: {
      showImage(uuid) {
        this.selected = uuid;
        $('#previewModal').modal('show');
      },

      reloadMedia() {
        var self = this;

        axios.get('/1.0/media?status=' + self.status).then(function(response) {
          if ( response.status === 200 ) {
            self.media = response.data;
          }
        });
      }
    }
  }
</script>