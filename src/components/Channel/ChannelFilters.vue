<template>
  <div class="row mt-4">
    <div class="col-md-10 offset-md-1 col-lg-8 offset-lg-2">
      <form
        class="form-inline d-flex"
        method="get"
        action=""
        @submit.prevent="searchChannels()"
      >
        <div class="form-flex">
          <div class="mt-style">
            <select
              name="topic"
              class="form-control"
              @change="searchChannels()"
              v-model="topic"
            >
              <option value="">Topic</option>
              <option
                v-for="topic in getTopics"
                :key="topic.topic_id"
                :value="topic.topic_id"
                >{{ topic.name }}</option
              >
            </select>
          </div>
        </div>
        <div class="form-flex">
          <div class="mt-style">
            <select
              name="style"
              class="form-control"
              @change="searchChannels()"
              v-model="style"
            >
              <option value="">Style</option>
              <option
                v-for="style in getStyles"
                :key="style.style_id"
                :value="style.style_id"
                >{{ style.name }}</option
              >
            </select>
          </div>
        </div>
        <div class="form-flex">
          <div class="input-group">
            <input
              type="search"
              class="form-control"
              placeholder="Search"
              aria-label="search"
              name="s"
              v-model.trim="search"
              @change="searchChannels()"
            />
            <div class="input-group-append">
              <i class="fas fa-search" @click="searchChannels()"></i>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import { mapGetters } from 'vuex';

export default {
  props: [],
  components: {},
  data() {
    return {
      channelsLoading: false,
      channelsPage: 0,
      channels: [],
      search: '',
      topic: '',
      style: '',
      topics: [],
      styles: []
    };
  },
  computed: {
    ...mapGetters(['getStyles', 'getTopics'])
  },
  created() {
    this.search = this.$route.query.tag;
  },
  methods: {
    searchChannels() {
      this.channelsLoading = true;

      let searchString = '?';

      if (this.order) {
        searchString += '&offset=' + this.channelsPage + '&order=' + this.order;
      } else {
        searchString += '&orderby=title&order=asc';
      }

      // If they are trying a hash search, just treat it as a normal search since we search both ATM
      if (this.search) {
        searchString += '&s=' + this.search.replace('#', '');
      }

      if (this.style) {
        searchString += '&style=' + this.style;
      }

      if (this.topic) {
        searchString += '&topic=' + this.topic;
      }

      fetch(process.env.VUE_APP_URL + 'channel/search.php' + searchString, {
        //mode: 'no-cors',
        method: 'GET',
        headers: { 'Content-Type': 'application/json' }
      })
        .then(response => {
          if (response.ok) {
            return response.json();
          }
        })
        .then(data => {
          this.channelsPage++;
          this.channelsLoading = false;
          this.$store.dispatch('updateChannels', data);
        })
        .catch(error => {
          //this.errorMessage = error;
          this.channelsLoading = false;
          console.error('There was an error!', error);
        });
    }
    // queryUpdate() {
    // 	this.$router.push({
    // 		name: 'Home',
    // 		query: {
    // 			order: 'asc',
    // 			search: 'search',
    // 			style: '',
    // 			topic: ''
    // 		}
    // 	});
    // }
  }
};
</script>

<style scoped>
.btn {
  display: inline-block;
  width: 110px;
}

.btn svg {
  margin-right: 10px;
  width: 19px;
  margin-top: -2px;
  display: inline-block;
}

.btn span {
  font-weight: bold;
  display: inline-block;
}

@media (max-width: 1199px) {
  form.form-inline {
    flex-wrap: wrap;
  }

  /* .form-flex,
		.form-flex-2 {
			flex: 0 1 50%;
			width: 50%;
			margin-bottom: 10px
		} */
}
</style>
