<template>
  <form class="card" @submit.prevent="validateChannel">
    <div class="card-body">
      <div class="form-group">
        <label for="title">Title</label>
        <input
          type="text"
          class="form-control"
          id="title"
          name="title"
          v-model.trim="channel.title"
          required
        />
      </div>

      <div class="form-group">
        <label for="youtube_id">Channel ID</label>
        <input
          type="text"
          class="form-control"
          id="youtube_id"
          name="youtube_id"
          v-model.trim="channel.youtube_id"
          required
        />
      </div>

      <div class="form-group">
        <label for="facebook">Facebook</label>
        <input
          type="url"
          class="form-control"
          id="facebook"
          name="facebook"
          v-model.trim="channel.facebook"
          placeholder="https://"
        />
      </div>

      <div class="form-group">
        <label for="instagram">Instagram</label>
        <input
          type="url"
          class="form-control"
          id="instagram"
          name="instagram"
          v-model.trim="channel.instagram"
          placeholder="https://"
        />
      </div>

      <div class="form-group">
        <label for="patreon">Patreon</label>
        <input
          type="url"
          class="form-control"
          id="patreon"
          name="patreon"
          v-model.trim="channel.patreon"
          placeholder="https://"
        />
      </div>

      <div class="form-group">
        <label for="tiktok">TikTok</label>
        <input
          type="url"
          class="form-control"
          id="tiktok"
          name="tiktok"
          v-model.trim="channel.tiktok"
          placeholder="https://"
        />
      </div>

      <div class="form-group">
        <label for="twitter">Twitter</label>
        <input
          type="url"
          class="form-control"
          id="twitter"
          name="twitter"
          v-model.trim="channel.twitter"
          placeholder="https://"
        />
      </div>

      <div class="form-group">
        <label for="twitch">Twitch</label>
        <input
          type="url"
          class="form-control"
          id="twitch"
          name="twitch"
          v-model.trim="channel.twitch"
          placeholder="https://"
        />
      </div>

      <div class="form-group">
        <label for="website">Website</label>
        <input
          type="url"
          class="form-control"
          id="website"
          name="website"
          v-model.trim="channel.website"
          placeholder="https://"
        />
      </div>

      <div class="row">
        <div class="col">
          <div class="form-group">
            <label for="style">Style</label>
            <div
              class="form-check"
              v-for="style in getStyles"
              :key="style.style_id"
            >
              <input
                class="form-check-input"
                :id="'style' + style.style_id"
                type="checkbox"
                :value="style.style_id"
                v-model="channel.styles"
              />
              <label class="form-check-label" :for="'style' + style.style_id">
                {{ style.name }}
              </label>
            </div>

            <!-- <select id="style" class="form-control" multiple v-model.trim="channel.styles">
							<option selected>Choose...</option>
							<option v-for="style in getStyles" :key="style.style_id" :value="style.style_id">{{ style.name }}</option>
						</select> -->
          </div>
        </div>
        <div class="col">
          <div class="form-group">
            <label for="topic">Topic</label>
            <div
              class="form-check"
              v-for="topic in getTopics"
              :key="topic.topic_id"
            >
              <input
                class="form-check-input"
                :id="'topic' + topic.topic_id"
                type="checkbox"
                :value="topic.topic_id"
                v-model="channel.topics"
              />
              <label class="form-check-label" :for="'topic' + topic.topic_id">
                {{ topic.name }}
              </label>
            </div>

            <!-- <select id="topic" class="form-control" multiple v-model.trim="channel.topics">
							<option selected>Choose...</option>
							<option v-for="topic in getTopics" :key="topic.topic_id" :value="topic.topic_id">{{ topic.name }}</option>
						</select> -->
          </div>
        </div>
      </div>

      <!-- <div class="form-group">
				<label for="tags">Tags</label>
				<input type="text" class="form-control" id="tags" name="tags" v-model.trim="tags" placeholder="#example1,#example2" />
			</div> -->
    </div>
    <div class="card-footer">
      <div :class="{ error: errors.length }">
        <div class="input-errors" v-for="(error, index) of errors" :key="index">
          <div class="error-msg">{{ error }}</div>
        </div>
      </div>
      <div class="row">
        <div class="col">
          <input
            type="reset"
            class="btn btn-secondary w-100"
            value="Clear Form"
            @click="clearForm"
          />
        </div>
        <div class="col">
          <base-button type="submit" class="btn btn-primary w-100"
            >{{ action }} Channel</base-button
          >
        </div>
      </div>
    </div>
  </form>
</template>

<script>
import { mapGetters } from 'vuex';

import BaseButton from '../UI/BaseButton.vue';

export default {
  inject: [],
  props: ['action', 'editChannel'],
  components: {
    BaseButton
  },
  data() {
    return {
      inputIsInvalid: false,
      errors: [],
      channel: {
        title: '',
        youtube_id: '',
        facebook: '',
        instagram: '',
        patreon: '',
        tiktok: '',
        twitter: '',
        twitch: '',
        website: '',
        //tags: '',
        styles: [],
        topics: []
        //focus: [],
      }
    };
  },
  computed: {
    ...mapGetters(['getStyles', 'getTopics'])
  },
  methods: {
    validateChannel() {
      if (!this.channel.title || this.channel.title.length < 3) {
        this.inputIsInvalid = true;
        this.errors.push('Title is Invalid');
      }

      if (!this.channel.youtube_id || this.channel.youtube_id.length < 5) {
        this.inputIsInvalid = true;
        this.errors.push('Channel ID is Invalid');
      }

      if (this.channel.facebook && !this.isValidUrl(this.channel.facebook)) {
        this.inputIsInvalid = true;
        this.errors.push('Facebook is Invalid');
      }

      if (this.channel.instagram && !this.isValidUrl(this.channel.instagram)) {
        this.inputIsInvalid = true;
        this.errors.push('Instagram is Invalid');
      }

      if (this.channel.patreon && !this.isValidUrl(this.channel.patreon)) {
        this.inputIsInvalid = true;
        this.errors.push('Patreon is Invalid');
      }

      if (this.channel.tiktok && !this.isValidUrl(this.channel.tiktok)) {
        this.inputIsInvalid = true;
        this.errors.push('Tiktok is Invalid');
      }

      if (this.channel.twitter && !this.isValidUrl(this.channel.twitter)) {
        this.inputIsInvalid = true;
        this.errors.push('Twitter is Invalid');
      }

      if (this.channel.twitch && !this.isValidUrl(this.channel.twitch)) {
        this.inputIsInvalid = true;
        this.errors.push('Twitch is Invalid');
      }

      if (this.channel.website && !this.isValidUrl(this.channel.website)) {
        this.inputIsInvalid = true;
        this.errors.push('Website is Invalid');
      }

      if (this.inputIsInvalid) {
        return false;
      }

      // This channel is validated so pass to parent
      this.$emit('channelAction', this.channel);

      //Iterate through each object field, key is name of the object field`
      // Object.keys(this.channel).forEach(function(key) {
      // 	self.channel[key] = '';
      // });
    },
    clearForm() {
      this.channel = {
        title: '',
        youtube_id: '',
        facebook: '',
        instagram: '',
        patreon: '',
        tiktok: '',
        twitter: '',
        twitch: '',
        website: '',
        //tags: '',
        styles: [],
        topics: []
        //focus: [],
      };
    }
  },
  watch: {
    editChannel(newChannel) {
      this.channel = newChannel;
    }
  }
};
</script>
