<template>
	<div id="add-channel">
		<h2>Add Channel</h2>
		<ChannelForm @channelAction="submitChannel" :action="'Add'"/>
		<LoadingIcon v-if="insertLoading" />
	</div>
</template>

<script>
// TODO: Turn the loading icon into a floating absolute position icon
import ChannelForm from './ChannelForm.vue';

import LoadingIcon from '../UI/LoadingIcon.vue';

export default {
	inject: [],
	components: {
		ChannelForm,
		LoadingIcon
	},
	data() {
		return {
			insertLoading: false,
			errorMessage: ''
		};
	},
	methods: {
		submitChannel(channel){
						
			this.insertLoading = true;

			fetch(process.env.VUE_APP_URL+'channel/add.php', {
				//mode: 'no-cors',
				method: 'POST',
				headers: { 'Content-Type': 'application/json' },
				body: JSON.stringify({ channel: channel })
			})
			.then(response => response.json())
			.then(data => {
				this.insertLoading = false;
				if(data.success){
					return true
				}
			})
			.catch(error => {
				this.insertLoading = false;
				this.errorMessage = error;
				console.error('There was an error!', error);
			});

		},
		
	}
}
</script>

<style scoped>
	/* .darkmode .loading-icon {
		color: #111111;
	} */
</style>