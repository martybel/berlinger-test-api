<template>
  <div>
    <upload :token="token"></upload>
    <media></media>
  </div>
</template>
<script>
  import Upload from './Upload.vue'
  import Media  from './Media.vue'

  export default {
    mounted() {
      console.log("Application loaded");
    },

    props: ['token'],
    components: {
      'upload': Upload,
      'media': Media
    }

  }
</script>