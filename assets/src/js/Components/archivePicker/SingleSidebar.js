import { Component, Fragment } from '@wordpress/element';
import { toSrcSet } from '../ImagePicker';

const __ = ( str ) => wp.i18n.__( str, 'planet4-master-theme-backend' );
const { apiFetch } = wp;
const wpImageLink = ( id ) => `${ window.location.href.split( '/wp-admin' )[ 0 ] }/wp-admin/post.php?post=${ id }&action=edit`;
const largestSize = ( image ) => image.original;
const renderDefinition = ( key, value ) => ( <div>
  <dt>{ key }</dt>
  <dd>{ value }</dd>
</div> );

export class SingleSidebar extends Component {
  constructor( props ) {
    super( props );
    this.state = {
      processingImages: false,
      processingError: null,
    };
  }

  async includeInWp( ids ) {
    try {
      this.setState( { processingImages: true } );
      return await apiFetch( {
        method: 'POST',
        path: '/planet4/v1/image-archive/transfer',
        data: {
          ids: ids,
          use_original_language: false,
        }
      } );
    } catch ( e ) {
      console.log( e );
      this.setState( { processingError: e } );
    } finally {
      this.setState( { processingImages: false } );
    }
  }

  render() {
    const {
      parent,
      onIncludeInWP = () => null,
    } = this.props;

    const image = parent.getSelectedImages()[ 0 ];

    const {
      processingError,
      processingImages
    } = this.state;

    const original = largestSize( image );

    return <Fragment>
      { !!processingError && (
        <div className={ "error" }>Error: { processingError.message }</div>
      ) }
      { !!processingImages && (
        <div className={ "info" }>Processing...</div>
      ) }
      { image.wordpress_id ? (
        <a
          target='_blank'
          href={ wpImageLink( image.wordpress_id ) }
        >Wordpress image #{ image.wordpress_id }</a>
      ) : (
        <button
          onClick={ async () => {
            const images = await this.includeInWp( [ image.id ] );
            onIncludeInWP( images );
          } }
        >
          { __( 'Include in WP' ) }
        </button>
      ) }
      <img
        srcSet={ toSrcSet( image.sizes ) }
        title={ image.title }
        alt={ image.title }
      />
      <dl className={ 'picker-sidebar-fields' }>
        { renderDefinition(
          __( 'URL' ),
          <a href={ original.url }>{ original.url } </a>
        ) }
        { renderDefinition(
          __( 'Dimensions' ),
          `${ original.width } x ${ original.height }`
        ) }
        { renderDefinition(
          __( 'Title' ),
          image.title
        ) }
        { renderDefinition(
          __( 'Caption' ),
          image.caption
        ) }
        { renderDefinition(
          __( 'Credit' ),
          image.credit
        ) }
        { renderDefinition(
          __( 'Original language title' ),
          image.original_language_title,
        ) }
        { renderDefinition(
          __( 'Original language description' ),
          image.original_language_description,
        ) }
      </dl>
    </Fragment>;
  }

}
