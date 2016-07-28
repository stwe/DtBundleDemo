<?php

namespace AppBundle\Datatables;

use Sg\DatatablesBundle\Datatable\View\AbstractDatatableView;
use Sg\DatatablesBundle\Datatable\View\Style;

/**
 * Class PostDatatable
 *
 * @package AppBundle\Datatables
*/
class PostDatatable extends AbstractDatatableView
{
    /**
     * {@inheritdoc}
     */
    public function getLineFormatter()
    {
        $router = $this->router;

        $formatter = function($line) use ($router) {
            $route = $router->generate('profile_show', array('id' => $line['createdBy']['id']));
            $line['createdBy']['username'] = '<a href="' . $route . '">' . $line['createdBy']['username'] . '</a>';

            return $line;
        };

        return $formatter;
    }

    /**
     * Get User.
     *
     * @return mixed|null
     */
    private function getUser()
    {
        if ($this->authorizationChecker->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->securityToken->getToken()->getUser();
        } else {
            return null;
        }
    }

    /**
     * Is admin.
     *
     * @return bool
     */
    private function isAdmin()
    {
        return $this->authorizationChecker->isGranted('ROLE_ADMIN');
    }

    /**
     * {@inheritdoc}
     */
    public function buildDatatable(array $options = array())
    {
        $users = $this->em->getRepository('AppBundle:User')->findAll();

        $this->topActions->set(array(
            'start_html' => '<div class="row"><div class="col-sm-3">',
            'end_html' => '<hr></div></div>',
            'actions' => array(
                array(
                    'route' => $this->router->generate('post_new'),
                    'label' => $this->translator->trans('datatables.actions.new'),
                    'icon' => 'glyphicon glyphicon-plus',
                    'attributes' => array(
                        'rel' => 'tooltip',
                        'title' => $this->translator->trans('datatables.actions.new'),
                        'class' => 'btn btn-primary',
                        'role' => 'button'
                    ),
                )
            )
        ));

        $this->features->set(array(
            'scroll_x' => false,
            'extensions' => array(
                'buttons' =>
                    array(
                        'colvis',
                        'excel',
                        'pdf' => array(
                            'extend' => 'pdf',
                            'exportOptions' => array(
                                // show only the following columns:
                                'columns' => array(
                                    '2', // title column
                                    '4', // visible column
                                    '5', // publishedAt column
                                    '6', // updatedAt column
                                    '7', // createdBy column
                                )
                            )
                        ),
                    ),
                'responsive' => true,
                'fixedHeader' => true,
            ),
            'highlight' => true,
            'highlight_color' => 'yellow'
        ));

        $this->ajax->set(array(
            'url' => $this->router->generate('post_results'),
            'type' => 'GET',
            'pipeline' => 5
        ));

        $this->options->set(array(
            'length_menu' => array(10, 25, 50, 100, -1),
            'class' => Style::BOOTSTRAP_3_STYLE,
            'individual_filtering' => true,
            'individual_filtering_position' => 'head',
            'use_integration_options' => true,
        ));

        $this->callbacks->set(array(
            'init_complete' => array(
                'template' => ':post:init.js.twig'
            )
        ));

        $this->events->set(array(
            'order' => array(
                'template' => ':post:order.js.twig'
            )
        ));

        $this->columnBuilder
            ->add(null, 'multiselect', array(
                'start_html' => '<div class="wrapper" id="testwrapper">',
                'end_html' => '</div>',
                'attributes' => array(
                    'class' => 'testclass',
                    'name' => 'testname',
                ),
                'add_if' => function() {
                    return $this->isAdmin();
                },
                'actions' => array(
                    array(
                        'route' => 'post_bulk_delete',
                        'render_if' => function() {
                            return $this->isAdmin();
                        },
                        'label' => 'Delete',
                        'icon' => 'fa fa-times',
                        'attributes' => array(
                            'rel' => 'tooltip',
                            'title' => 'Delete',
                            'class' => 'btn btn-primary btn-xs',
                            'role' => 'button'
                        ),
                    )
                )
            ))
            ->add('id', 'column', array(
                'title' => 'Id',
                'filter' => array('text', array(
                    'search_type' => 'eq'
                ))
            ))
            ->add('title', 'column', array(
                'title' => 'Title',
                'editable' => true,
                'editable_if' => function($row) {
                    // caution the line $row['createdBy']['username'] is already formatted in the lineFormatter
                    if ($row['createdBy']['id'] == $this->getUser()->getId() or true === $this->isAdmin()) {
                        return true;
                    };

                    return false;
                },
            ))
            ->add('images.fileName', 'gallery', array(
                'title' => 'Images',
                'relative_path' => 'images',
                'imagine_filter' => 'thumbnail_50_x_50',
                'imagine_filter_enlarged' => 'thumbnail_250_x_250',
                'enlarge' => true,
                'holder_url' => 'https://placehold.it',
                'holder_width' => '50',
                'holder_height' => '50',
                'view_limit' => 2,
            ))
            ->add('visible', 'boolean', array(
                'title' => 'Visible',
                'true_label' => 'Yes',
                'false_label' => 'No',
                'filter' => array('select', array(
                    'search_type' => 'eq',
                    'select_options' => array('' => 'All', '1' => 'Yes', '0' => 'No')
                ))
            ))
            ->add('rating', 'progress_bar', array(
                'title' => 'Rating',
                'value_min' => '1',
                'value_max' => '5',
                'bar_classes' => 'progress-bar-striped',
                'filter' => array('slider', array(
                    'min' => 1.0,
                    'max' => 5.0,
                    'cancel_button' => true,
                    'handle' => 'custom' // display content like unicode characters or fontawesome icons
                    /* @see css rule in base.html.twig
                    .slider-handle.custom::before {
                        content: "\f005";
                        font-family: FontAwesome;
                        left: -5px;
                        position: absolute;
                        top: 0;
                        color: dodgerblue;
                    }
                    */
                ))
            ))
            ->add('publishedAt', 'datetime', array(
                'title' => 'Published at',
                'filter' => array('daterange', array())
            ))
            ->add('createdBy.username', 'column', array(
                'title' => 'Created By',
                'filter' => array('select2', array(
                    'search_type' => 'eq',
                    'select_options' => $this->getCollectionAsOptionsArray($users, 'username', 'username'),
                ))
            ))
            ->add(null, 'action', array(
                'title' => $this->translator->trans('datatables.actions.title'),
                'actions' => array(
                    array(
                        'route' => 'post_show',
                        'route_parameters' => array(
                            'id' => 'id'
                        ),
                        'label' => $this->translator->trans('datatables.actions.show'),
                        'icon' => 'glyphicon glyphicon-eye-open',
                        'attributes' => array(
                            'rel' => 'tooltip',
                            'title' => $this->translator->trans('datatables.actions.show'),
                            'class' => 'btn btn-primary btn-xs',
                            'role' => 'button'
                        ),
                    ),
                    array(
                        'route' => 'post_edit',
                        'route_parameters' => array(
                            'id' => 'id'
                        ),
                        'label' => $this->translator->trans('datatables.actions.edit'),
                        'icon' => 'glyphicon glyphicon-edit',
                        'attributes' => array(
                            'rel' => 'tooltip',
                            'title' => $this->translator->trans('datatables.actions.edit'),
                            'class' => 'btn btn-primary btn-xs',
                            'role' => 'button'
                        ),
                        'render_if' => function($row) {
                            // caution the line $row['createdBy']['username'] is already formatted in the lineFormatter
                            if ($row['createdBy']['id'] == $this->getUser()->getId() or true === $this->isAdmin()) {
                                return true;
                            };

                            return false;
                        },
                    )
                )
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getEntity()
    {
        return 'AppBundle\Entity\Post';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'post_datatable';
    }
}
