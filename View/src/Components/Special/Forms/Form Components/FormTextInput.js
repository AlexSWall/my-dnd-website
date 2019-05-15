import React, { Component } from 'react';

class FormTextInput extends Component 
{
	constructor(props) {
		super(props);
		this.state = {
			value: ''
		};
	}

	onChange = (event) => {
		if ( event.target.value.length <= 30)
			this.setState({value: event.target.value});
	};

	render() {
		return (
			<div className='form-group'>
				<label className='form-label' htmlFor={this.props.formId}>{ this.props.labelText }</label>
				<input
					className='form-control'
					type='text'
					name={this.props.formId}
					id={this.props.formId}
					value={this.state.value}
					onChange={this.onChange}
				/>
			</div>
		);
	}
}

export default FormTextInput;