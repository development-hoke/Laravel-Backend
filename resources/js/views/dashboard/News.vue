<template>
	<div>
		<v-container  fluid grid-list-xl pt-0>
			<v-row>
				<v-col xl="12" md="12" lg="12" sm="12" cols="12" class="pa-0">
					<div>
						<trending-news></trending-news>
					</div>
				</v-col>
			</v-row>
			<v-row>
			<app-card
				:heading="$t('message.topHeadlines')"
				:closeable="true"
				:fullScreen="true"
				:reloadable="true"
				:fullBlock="true"
				:footer="true"
				colClasses="col-xl-8 col-lg-8 col-md-7 col-sm-12 col-12"
				customClasses="top-headlines-widget"
			>
				<top-headlines></top-headlines>
				<div slot="footer">
					<v-btn color="success" small>{{$t('message.addNew')}}</v-btn>
				</div>
			</app-card>
			<v-col xl="4" md="5" lg="4" sm="12" cols="12">
				<v-row>
					<app-card
					:heading="$t('message.visitors')"
					colClasses="col-xl-12 col-lg-12 col-md-12 col-sm-6 col-12"
					customClasses="visitors-widget"
					>
						<visitors></visitors>
					</app-card>
					<app-card
						colClasses="col-xl-12 col-lg-12 col-md-12 col-sm-6 col-12"
						:heading="$t('message.subscribers')"
						customClasses="subscribers-widget"
					>
						<subscribers></subscribers>
					</app-card>
				</v-row>
			</v-col>
			</v-row>
			<v-row>
			<app-card
				:heading="$t('message.newsletterCampaign')"
				colClasses="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12"
				>
					<news-letter-campaign-v2
					:height="340"
					:width="596"
					:color1="ChartConfig.color.info"
					:color2="ChartConfig.color.primary"
					:label1="newsLetterCampaignData3.label1"
					:label2="newsLetterCampaignData3.label2"
					:dataLabels="newsLetterCampaignData3.labels"
					:dataSet1="newsLetterCampaignData3.data1"
					:dataSet2="newsLetterCampaignData3.data2"
					>
					</news-letter-campaign-v2>
			</app-card>
			<app-card
				:heading="$t('message.recentComments')"
				:closeable="true"
				:fullScreen="true"
				:reloadable="true"
				:fullBlock="true"
				:footer="true"
				colClasses="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12"
			>
				<recent-comments></recent-comments>
				<div slot="footer"><v-btn small color="primary">{{$t('message.viewAll')}}</v-btn></div>
			</app-card>
			</v-row>
			<!-- Social Feeds -->
				<v-row>
					<app-card
						colClasses="col-xl-3 col-lg-3 col-md-3 col-sm-6 col-12"
						customClasses="social-share-wrap"
					>
						<social-feeds
							socialIcon="zmdi zmdi-facebook"
							friends="89K"
							feeds="459"
							type="facebook"
						>
						</social-feeds>
					</app-card>
					<app-card
						colClasses="col-xl-3 col-lg-3 col-md-3 col-sm-6 col-12"
						customClasses="social-share-wrap"
					>
						<social-feeds
							socialIcon="zmdi zmdi-twitter"
							friends="89K"
							feeds="459"
							type="twitter"
						>
						</social-feeds>
					</app-card>
					<app-card
						colClasses="col-xl-3 col-lg-3 col-md-3 col-sm-6 col-12"
						customClasses="social-share-wrap"
					>
						<social-feeds
							socialIcon="zmdi zmdi-linkedin"
							friends="89K"
							feeds="459"
							type="linkedin"
						>
						</social-feeds>
					</app-card>
					<app-card
						colClasses="col-xl-3 col-lg-3 col-md-3 col-sm-6 col-12"
						customClasses="social-share-wrap"
						>
							<social-feeds
								socialIcon="zmdi zmdi-google"
								friends="89K"
								feeds="459"
								type="google"
							>
							</social-feeds>
						</app-card>
					</v-row>
				<v-row>
					<v-col xl="4" lg="4" md="4" sm="12" cols="12">
						<activity></activity>
					</v-col>
					<app-card
						colClasses="col-xl-8 col-lg-8 col-md-8 col-sm-12 col-12"
						:withTabs="true"
						:fullBlock="true"
						customClasses="top-news-wrap"
					>
						<top-news></top-news>
					</app-card>
				</v-row>
				<v-row>
					<app-card
						:fullBlock="true"
						colClasses="col-xl-4 col-lg-4 col-md-4 col-sm-6 col-12 col-height-auto"
						class="top-author-wrap"
					>
						<top-authors></top-authors>
					</app-card>
					<app-card
						colClasses="col-xl-4 col-lg-4 col-md-4 col-12 col-sm-6"
						customClasses="twitter-feeds-widget"
						heading="Twitter Feeds"
					>
						<twitter></twitter>
					</app-card>
						<app-card
							:fullBlock="true"
							colClasses="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-12"
						>
							<notifications-v2></notifications-v2>
					</app-card>
				</v-row>
			</v-container>
		</div>
	</template>

	<script>
		import TrendingNews from "Components/Widgets/TrendingNews";
		import TopHeadlines from "Components/Widgets/TopHeadlines";
		import Visitors from "Components/Widgets/Visitors";
		import Subscribers from "Components/Widgets/Subscribers";
		import SocialFeeds from "Components/Widgets/SocialFeeds";
		import Activity from "Components/Widgets/Activity";
		import TopNews from "Components/Widgets/TopNews";
		import NewsLetterCampaignV2 from "Components/Charts/NewsLetterCampaignV2";
		import TopAuthors from "Components/Widgets/TopAuthors";
		import Twitter from "Components/Widgets/Twitter";
		import RecentComments from "Components/Widgets/RecentComments";
		import NotificationsV2 from "Components/Widgets/NotificationsV2";

		// chart config
		import { ChartConfig } from "Constants/chart-config";
		// data
		import {
		newsLetterCampaignData3,
		} from "./data";

		export default {
		data(){
			return {
				ChartConfig,
				newsLetterCampaignData3
			}
		},
		components: {
			TrendingNews,
			TopHeadlines,
			Visitors,
			Subscribers,
			SocialFeeds,
			Activity,
			TopNews,
			NewsLetterCampaignV2,
			TopAuthors,
			Twitter,
			RecentComments,
			NotificationsV2
		}
	};
</script>
