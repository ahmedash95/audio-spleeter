<template>
	<div class="container">
		<div class="row justify-content-center">
			<div class="col-md-10">
				<div class="card">
					<div class="card-header">Audio Upload</div>
					<div class="card-body alert alert-warning" v-if="!authed">
						In order to upload any media you should be logged in first
					</div>
					<div class="card-body" v-if="authed">
						<div class="form-group col-md-12">
							<label for="fileUpload">Upload Audio (Max 15MB)</label>
							<input type="file" class="form-control-file" id="fileUpload" @change="fileUpload()" ref="file">
						</div>
						<div class="clearfix"></div>
						<div class="row">
							<div class="col">
								<div class="progress" v-if="uploadProgress">
									<div class="progress-bar" role="progressbar" :style="'width: '+uploadProgress+'%'"
										 :aria-valuenow="uploadProgress" aria-valuemin="0" aria-valuemax="100"></div>
								</div>
							</div>
							<div class="col-md-auto">
								<button class="btn-primary btn btn-sm" @click="submit()" :disabled="uploadProgress !== false">Upload</button>
							</div>
						</div>
						<br>
						<div class="row" v-if="errors.length > 0">
							<div class="col">
								<ul class="alert alert-danger">
									<li v-for="error in errors">{{ error }}</li>
								</ul>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
	export default {
		data() {
			return {
				file: null,
				uploadProgress: false,
				errors: [],
				authed: window.authed,
			}
		},
		mounted() {
			if(!window.authed) return;
			Echo.private(window.userChannel)
			.listen('.audio.uploaded', (event) => {
				this.$root.$emit('list.new',event.audio);
			})
		},
		methods: {
			fileUpload(event) {
				this.file = this.$refs.file.files[0];
			},
			submit: function () {
				this.errors = [];
				let formData = new FormData();
				formData.append('file', this.file);
				axios.post('/api/audio/',formData,{
					headers: {
						'Content-Type': 'multipart/form-data'
					},
					onUploadProgress: function( progressEvent ) {
						this.uploadProgress = parseInt( Math.round( ( progressEvent.loaded * 100 ) / progressEvent.total ) );
					}.bind(this)
				}).catch((error) => {
					this.errors = error.response.data.errors.file
				})
				.finally(() => {
					this.uploadProgress = false;
				});
			}
		}
	}
</script>
